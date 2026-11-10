<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the ecommerce login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('ecommerce.shop');
        }

        return view('ecommerce.pages.login');
    }

    /**
     * Show the ecommerce registration form.
     */
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('ecommerce.shop');
        }

        return view('ecommerce.pages.register');
    }

    /**
     * Handle ecommerce registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'firstname' => ['nullable', 'string', 'max:120'],
            'lastname' => ['nullable', 'string', 'max:120'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $firstName = trim((string) $request->input('firstname', ''));
        $lastName = trim((string) $request->input('lastname', ''));
        $fullName = trim("{$firstName} {$lastName}");

        if ($fullName === '') {
            $fullName = trim((string) $request->input('name', ''));
        }

        if ($fullName === '') {
            $fullName = explode('@', (string) $request->input('email'))[0];
        }

        $user = User::query()->create([
            'name' => $fullName,
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'role' => 'user',
            'last_logged_in' => now(),
        ]);

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('user');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('ecommerce.shop')
            ->with('success', __('Registration successful. Welcome, :name!', ['name' => $user->name]));
    }

    /**
     * Handle ecommerce login attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var User|null $user */
            $user = Auth::user();
            if ($user !== null) {
                $user->update(['last_logged_in' => now()]);
            }

            return redirect()
                ->intended(route('ecommerce.shop'))
                ->with('success', __('Login successful. Welcome back, :name!', ['name' => $user?->name]));
        }

        return back()
            ->withErrors([
                'email' => __('auth.failed'),
            ])
            ->onlyInput('email');
    }

    /**
     * Handle ecommerce logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('ecommerce.login')
            ->with('status', __('You have been logged out.'));
    }
}
