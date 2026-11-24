<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Exports\UsersTemplateExport;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Imports\UsersImport;
use App\Models\LoginHistory;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('users.view');

        $users = User::query()->with('roles')->latest()->get();

        return view('modules.users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('users.create');

        $roles = Role::query()->orderBy('name')->get();

        return view('modules.users.create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        Gate::authorize('users.create');

        $user = User::query()->create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'role' => 'user',
        ]);

        if ($request->filled('roles')) {
            $user->syncRoles($request->validated('roles'));
            $user->update(['role' => $user->roles()->first()?->name ?? 'user']);
        }

        return redirect()
            ->route('users.index')
            ->with('success', __('User created successfully.'));
    }

    /**
     * Duplicate the specified resource.
     */
    public function duplicate(User $user): RedirectResponse
    {
        Gate::authorize('users.create');

        $emailParts = explode('@', $user->email);
        $newEmail = $emailParts[0].'+copy'.rand(100, 999).'@'.($emailParts[1] ?? 'example.com');
        while (User::where('email', $newEmail)->exists()) {
            $newEmail = $emailParts[0].'+copy'.rand(1000, 99999).'@'.($emailParts[1] ?? 'example.com');
        }

        $newUser = User::query()->create([
            'name' => $user->name.' (Copy)',
            'email' => $newEmail,
            'password' => $user->password,
            'role' => $user->role,
        ]);

        $roleNames = $user->roles->pluck('name')->all();
        if (! empty($roleNames)) {
            $newUser->syncRoles($roleNames);
        }

        return redirect()
            ->route('users.index')
            ->with('success', __('User duplicated successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        Gate::authorize('users.view');

        $user->load(['roles.permissions', 'permissions']);

        return view('modules.users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        Gate::authorize('users.edit');

        $roles = Role::query()->orderBy('name')->get();

        return view('modules.users.edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('users.edit');

        $data = [
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->validated('password'));
        }

        $user->update($data);

        $user->syncRoles($request->input('roles', []));
        $user->update(['role' => $user->roles()->first()?->name ?? 'user']);

        return redirect()
            ->route('users.index')
            ->with('success', __('User updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('users.delete');

        if (auth()->id() === $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', __('You cannot delete your own account.'));
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', __('User deleted successfully.'));
    }

    /**
     * Export users to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('users.view');

        return Excel::download(new UsersExport, 'users_'.date('Y-m-d_His').'.xlsx');
    }

    /**
     * Export users to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('users.view');

        $users = User::query()->with('roles')->latest('id')->get();

        return Pdf::loadView('modules.users.pdf', [
            'users' => $users,
        ])->setPaper('a4', 'portrait')->download('users_'.date('Y-m-d_His').'.pdf');
    }

    /**
     * Download user import template.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('users.create');

        return Excel::download(new UsersTemplateExport, 'users_import_example.xlsx');
    }

    /**
     * Import users from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('users.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            Excel::import(new UsersImport, $request->file('file'));

            return redirect()
                ->route('users.index')
                ->with('success', __('Data imported successfully.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('users.index')
                ->with('error', __('Import failed: ').$e->getMessage());
        }
    }

    /**
     * Force login (impersonate) as the specified user.
     */
    public function forceLogin(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('users.edit');

        if (config('stisla.is_demo')) {
            return redirect()
                ->route('users.index')
                ->with('error', __('Action is disabled in demo mode.'));
        }

        /** @var User $admin */
        $admin = $request->user();

        if ($admin->id === $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', __('You are already logged in as this user.'));
        }

        if ($user->isBlocked()) {
            return redirect()
                ->route('users.index')
                ->with('error', __('Cannot force login to a blocked user.'));
        }

        $impersonatorId = $request->session()->get('impersonator_id', $admin->id);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('impersonator_id', $impersonatorId);

        LoginHistory::record(
            event: 'force_login',
            status: 'success',
            user: $user,
            email: $user->email,
            notes: __('Force logged in by :admin', ['admin' => $admin->email]),
        );

        return redirect()
            ->route('dashboard')
            ->with('success', __('Now logged in as :name.', ['name' => $user->name]));
    }

    /**
     * Stop impersonating and return to the original admin account.
     */
    public function stopImpersonate(Request $request): RedirectResponse
    {
        $impersonatorId = $request->session()->pull('impersonator_id');

        if (! $impersonatorId) {
            return redirect()->route('dashboard');
        }

        $admin = User::query()->find($impersonatorId);
        if (! $admin) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        }

        Auth::login($admin);
        $request->session()->regenerate();

        return redirect()
            ->route('users.index')
            ->with('success', __('Returned to admin account.'));
    }

    /**
     * Block the specified user and revoke active sessions.
     */
    public function block(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('users.edit');

        if (config('stisla.is_demo')) {
            return redirect()
                ->route('users.index')
                ->with('error', __('Action is disabled in demo mode.'));
        }

        if (auth()->id() === $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', __('You cannot block your own account.'));
        }

        $validated = $request->validate([
            'blocked_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'is_blocked' => true,
            'blocked_at' => now(),
            'blocked_reason' => $validated['blocked_reason'] ?? null,
        ]);

        if (Schema::hasTable('sessions')) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return redirect()
            ->route('users.index')
            ->with('success', __('User blocked successfully.'));
    }

    /**
     * Unblock the specified user.
     */
    public function unblock(User $user): RedirectResponse
    {
        Gate::authorize('users.edit');

        if (config('stisla.is_demo')) {
            return redirect()
                ->route('users.index')
                ->with('error', __('Action is disabled in demo mode.'));
        }

        $user->update([
            'is_blocked' => false,
            'blocked_at' => null,
            'blocked_reason' => null,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', __('User unblocked successfully.'));
    }
}
