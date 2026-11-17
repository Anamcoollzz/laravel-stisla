<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

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
        $user = User::query()->create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => 'user',
            'last_logged_in' => now(),
        ]);

        $user->assignRole('user');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', __('Registration successful.'));
    }
}
