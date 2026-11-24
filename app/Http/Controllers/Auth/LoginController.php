<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\ActivityLog;
use App\Models\LoginHistory;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Show the application's login form.
     */
    public function showLoginForm(Request $request): View
    {
        $requested = $request->query('layout');
        if (in_array($requested, ['default', 'login-2'], true)) {
            $layout = $requested;
        } else {
            $layout = Setting::getValue('login_layout', 'default');
        }

        $view = $layout === 'login-2' ? 'auth.login-2' : 'auth.login';

        return view($view);
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->safe()->only(['email', 'password']);
        $email = (string) $credentials['email'];
        $throttleKey = $this->throttleKey($request, $email);
        $maxAttempts = max(1, (int) (Setting::getValue('login_max_attempts', '5') ?: 5));
        $decayMinutes = max(1, (int) (Setting::getValue('login_decay_minutes', '1') ?: 1));

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            event(new Lockout($request));

            $this->recordFailedLogin(
                action: 'login_locked',
                email: $email,
                description: "Admin login temporarily locked for {$email} ({$seconds}s remaining)",
                before: ['attempts' => RateLimiter::attempts($throttleKey)],
                after: ['locked_for_seconds' => $seconds, 'max_attempts' => $maxAttempts],
            );

            return back()
                ->withErrors([
                    'email' => __('Too many login attempts. Please try again in :seconds seconds.', [
                        'seconds' => $seconds,
                    ]),
                ])
                ->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            /** @var User $user */
            $user = Auth::user();

            if ($user->isBlocked()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                RateLimiter::hit($throttleKey, $decayMinutes * 60);

                $this->recordFailedLogin(
                    action: 'login_blocked',
                    email: $email,
                    description: "Blocked user login attempt for {$email}",
                    before: ['is_blocked' => true],
                    after: ['attempts' => RateLimiter::attempts($throttleKey)],
                    matchedUser: $user,
                );

                return back()
                    ->with('error', __('Your account has been blocked.'))
                    ->onlyInput('email');
            }

            if (Setting::getValue('send_activation_email_on_register', '0') === '1'
                && $user->email_verified_at === null
                && ! $user->hasRole('admin')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                RateLimiter::hit($throttleKey, $decayMinutes * 60);

                $this->recordFailedLogin(
                    action: 'login_unverified',
                    email: $email,
                    description: "Blocked unverified admin login attempt for {$email}",
                    before: ['email_verified' => false],
                    after: ['attempts' => RateLimiter::attempts($throttleKey)],
                    matchedUser: $user,
                );

                return back()
                    ->with('error', __('Akun Anda belum aktif. Silakan cek email Anda dan klik link aktivasi terlebih dahulu.'))
                    ->onlyInput('email');
            }

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            $user->update(['last_logged_in' => now()]);

            LoginHistory::record(
                event: 'login_success',
                status: 'success',
                user: $user,
                email: $user->email,
                notes: $request->boolean('remember') ? __('Remember Me enabled') : null,
            );

            return redirect()
                ->intended(route('dashboard'))
                ->with('success', __('Login successful.'));
        }

        RateLimiter::hit($throttleKey, $decayMinutes * 60);
        $attempts = RateLimiter::attempts($throttleKey);
        $remaining = max(0, $maxAttempts - $attempts);

        $this->recordFailedLogin(
            action: 'login_failed',
            email: $email,
            description: "Failed admin login attempt for {$email} ({$attempts}/{$maxAttempts})",
            before: ['attempts' => max(0, $attempts - 1)],
            after: [
                'attempts' => $attempts,
                'remaining_attempts' => $remaining,
                'max_attempts' => $maxAttempts,
            ],
        );

        $errorMessage = $remaining > 0
            ? __('auth.failed').' '.__('(:remaining attempt(s) remaining before temporary lockout.)', ['remaining' => $remaining])
            : __('auth.failed').' '.__('Maximum login attempts reached. Please wait :minutes minute(s) before trying again.', ['minutes' => $decayMinutes]);

        return back()
            ->withErrors([
                'email' => $errorMessage,
            ])
            ->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        if ($user) {
            LoginHistory::record(
                event: 'logout',
                status: 'success',
                user: $user,
                email: $user->email,
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(Request $request, string $email): string
    {
        return Str::transliterate(Str::lower($email).'|'.$request->ip());
    }

    /**
     * Record a failed or locked login attempt in ActivityLog.
     *
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     */
    protected function recordFailedLogin(
        string $action,
        string $email,
        string $description,
        ?array $before = null,
        ?array $after = null,
        ?User $matchedUser = null,
    ): void {
        if (! Schema::hasTable('activity_logs')) {
            return;
        }

        $matchedUser ??= User::query()->where('email', $email)->first(['id', 'email']);

        LoginHistory::record(
            event: $action,
            status: $action === 'login_failed' ? 'failed' : 'blocked',
            user: $matchedUser,
            email: $email,
            notes: $description,
        );

        ActivityLog::record(
            module: 'auth',
            action: $action,
            description: $description,
            subject: $matchedUser,
            subjectLabel: $email,
            before: $before,
            after: $after,
            subjectId: $matchedUser ? (string) $matchedUser->id : $email,
            subjectType: User::class,
        );
    }
}
