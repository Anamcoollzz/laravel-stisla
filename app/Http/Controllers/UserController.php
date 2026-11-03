<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

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
}
