<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('guests are redirected to login when accessing profile', function () {
    $this->get(route('profile.edit'))
        ->assertRedirect(route('login'));
});

test('authenticated user can view profile page with their identity and avatar', function () {
    $user = $this->actingAsAdmin();

    $this->get(route('profile.edit'))
        ->assertOk()
        ->assertViewIs('profile.edit')
        ->assertSee($user->name)
        ->assertSee($user->email)
        ->assertSee('Admin')
        ->assertSee('id="avatar-preview"', false)
        ->assertSee('name="current_password"', false)
        ->assertSee('id="btn-generate-password"', false)
        ->assertSee('id="btn-copy-password"', false)
        ->assertSee('btn-toggle-pw', false);
});

test('user can update profile name and email', function () {
    $user = $this->actingAsAdmin();

    $this->put(route('profile.update'), [
        'name' => 'Super Administrator Updated',
        'email' => 'superadmin.updated@example.com',
    ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success');

    $user->refresh();
    expect($user->name)->toBe('Super Administrator Updated');
    expect($user->email)->toBe('superadmin.updated@example.com');
});

test('user cannot update email to an already taken email', function () {
    User::factory()->create(['email' => 'existing@example.com']);
    $user = $this->actingAsAdmin();

    $this->from(route('profile.edit'))->put(route('profile.update'), [
        'name' => $user->name,
        'email' => 'existing@example.com',
    ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHasErrors('email');
});

test('user can upload avatar image file', function () {
    Storage::fake('public');
    $user = $this->actingAsAdmin();

    $file = UploadedFile::fake()->image('my-avatar.jpg', 200, 200);

    $this->put(route('profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'avatar' => $file,
    ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success');

    $user->refresh();
    expect($user->avatar)->not->toBeNull();
    Storage::disk('public')->assertExists($user->avatar);
});

test('user can remove custom avatar and restore default', function () {
    Storage::fake('public');
    $user = $this->actingAsAdmin();

    $file = UploadedFile::fake()->image('avatar.png', 100, 100);
    $path = $file->store('avatars', 'public');
    $user->update(['avatar' => $path]);

    $this->put(route('profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'remove_avatar' => '1',
    ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success');

    $user->refresh();
    expect($user->avatar)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('user can update password with valid current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password-123'),
        'role' => 'admin',
    ]);
    $this->actingAsAdmin($user);

    $this->put(route('profile.password.update'), [
        'current_password' => 'old-password-123',
        'password' => 'new-secret-password-456',
        'password_confirmation' => 'new-secret-password-456',
    ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success');

    $user->refresh();
    expect(Hash::check('new-secret-password-456', $user->password))->toBeTrue();
});

test('user cannot update password with invalid current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('correct-password-123'),
        'role' => 'admin',
    ]);
    $this->actingAsAdmin($user);

    $this->from(route('profile.edit'))->put(route('profile.password.update'), [
        'current_password' => 'wrong-password-999',
        'password' => 'new-secret-password-456',
        'password_confirmation' => 'new-secret-password-456',
    ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHasErrors('current_password');

    $user->refresh();
    expect(Hash::check('correct-password-123', $user->password))->toBeTrue();
});
