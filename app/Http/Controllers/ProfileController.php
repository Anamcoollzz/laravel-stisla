<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the user profile settings page.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load('roles');

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user profile information and avatar.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->safe()->only(['name', 'email']);

        if ($request->boolean('remove_avatar') && $user->avatar) {
            if (Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = null;
        }

        if ($request->hasFile('avatar')) {
            try {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $path = $request->file('avatar')->store('avatars', 'public');
                $data['avatar'] = $path;
            } catch (\Throwable $e) {
                return redirect()
                    ->route('profile.edit')
                    ->with('error', __('Gagal menyimpan file avatar: ').$e->getMessage());
            }
        }

        $user->update($data);

        return redirect()
            ->route('profile.edit')
            ->with('success', __('Profile updated successfully.'));
    }

    /**
     * Update the user password.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', __('Password updated successfully.'));
    }
}
