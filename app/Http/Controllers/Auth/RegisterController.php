<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Mail\AccountActivationMail;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Throwable;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Setting::getValue('is_active_register_page', '1') !== '1') {
            return redirect()
                ->route('login')
                ->with('error', __('Halaman registrasi saat ini sedang dinonaktifkan.'));
        }

        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        if (Setting::getValue('is_active_register_page', '1') !== '1') {
            return redirect()
                ->route('login')
                ->with('error', __('Halaman registrasi saat ini sedang dinonaktifkan.'));
        }

        $sendActivation = Setting::getValue('send_activation_email_on_register', '0') === '1';

        $user = User::query()->create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => 'user',
            'email_verified_at' => $sendActivation ? null : now(),
            'last_logged_in' => $sendActivation ? null : now(),
        ]);

        $user->assignRole('user');

        if ($sendActivation) {
            $activationUrl = URL::temporarySignedRoute(
                'register.activate',
                now()->addHours(24),
                [
                    'id' => $user->id,
                    'hash' => sha1($user->email),
                ]
            );

            try {
                Mail::to($user->email)->send(new AccountActivationMail($user, $activationUrl));
            } catch (Throwable $e) {
                Log::error('Failed to send activation email: '.$e->getMessage());

                return redirect()
                    ->route('login')
                    ->with('error', __('Akun berhasil dibuat, namun gagal mengirim email aktivasi. Silakan hubungi admin.'));
            }

            return redirect()
                ->route('login')
                ->with('success', __('Registrasi berhasil! Silakan cek email Anda untuk mengaktifkan akun sebelum login.'));
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', __('Registration successful.'));
    }

    /**
     * Activate a newly registered user account via signed email link.
     */
    public function activate(int $id, string $hash): RedirectResponse
    {
        $user = User::query()->findOrFail($id);

        if (! hash_equals(sha1($user->email), $hash)) {
            abort(403, __('Link aktivasi tidak valid.'));
        }

        if ($user->email_verified_at === null) {
            $user->update(['email_verified_at' => now()]);
        }

        return redirect()
            ->route('login')
            ->with('success', __('Akun Anda berhasil diaktifkan! Silakan login.'));
    }
}
