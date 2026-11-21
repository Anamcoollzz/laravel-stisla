<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect(): RedirectResponse
    {
        if (blank(config('services.google.client_id')) || blank(config('services.google.client_secret'))) {
            return redirect()
                ->route('login')
                ->with('error', __('Google OAuth is not configured yet.'));
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google and sign in or register.
     */
    public function callback(Request $request): RedirectResponse
    {
        if (blank(config('services.google.client_id')) || blank(config('services.google.client_secret'))) {
            return redirect()
                ->route('login')
                ->with('error', __('Google OAuth is not configured yet.'));
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::error('Google OAuth callback failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'redirect' => config('services.google.redirect'),
                'host' => $request->getSchemeAndHttpHost(),
            ]);

            return redirect()
                ->route('login')
                ->with('error', __('Unable to authenticate with Google. Please try again.'));
        }

        $email = Str::lower((string) $googleUser->getEmail());
        if (blank($email)) {
            return redirect()
                ->route('login')
                ->with('error', __('Google account did not provide a valid email address.'));
        }

        $user = User::query()
            ->where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            $user->update([
                'google_id' => $user->google_id ?: $googleUser->getId(),
                'email_verified_at' => $user->email_verified_at ?: now(),
                'last_logged_in' => now(),
            ]);
        } else {
            if (Setting::getValue('is_active_register_page', '1') !== '1') {
                return redirect()
                    ->route('login')
                    ->with('error', __('Registration is currently disabled.'));
            }

            $user = User::query()->create([
                'name' => $googleUser->getName() ?: Str::before($email, '@'),
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(32)),
                'role' => 'user',
                'last_logged_in' => now(),
            ]);

            if (Role::query()->where('name', 'user')->exists()) {
                $user->assignRole('user');
            }
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()
            ->intended(route('dashboard'))
            ->with('success', __('Login successful.'));
    }
}
