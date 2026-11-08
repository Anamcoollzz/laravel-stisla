<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->actingAsAdmin());

test('users index page displays users in datatable with roles and action buttons', function () {
    $role = Role::firstOrCreate(['name' => 'operator', 'guard_name' => 'web']);
    $user = User::factory()->create([
        'name' => 'John Operator',
        'email' => 'operator@example.com',
    ]);
    $user->assignRole($role);

    $this->get(route('users.index'))
        ->assertOk()
        ->assertViewIs('modules.users.index')
        ->assertSee('id="table-users"', false)
        ->assertSee('Daftar Pengguna')
        ->assertSee('Tambah Baru')
        ->assertSee('John Operator')
        ->assertSee('operator@example.com')
        ->assertSee('operator');
});

test('users create page renders form with roles list', function () {
    $this->get(route('users.create'))
        ->assertOk()
        ->assertViewIs('modules.users.create')
        ->assertSee('Tambah Pengguna')
        ->assertSee('name="name"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="password"', false)
        ->assertSee('name="password_confirmation"', false)
        ->assertSee('name="roles[]"', false);
});

test('can store a new user with hashed password and assigned roles', function () {
    $this->post(route('users.store'), [
        'name' => 'Budi Santoso',
        'email' => 'budi.santoso@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
        'roles' => ['staff'],
    ])
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success', 'Pengguna berhasil ditambahkan.');

    $user = User::where('email', 'budi.santoso@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Budi Santoso')
        ->and(Hash::check('secret123', $user->password))->toBeTrue()
        ->and($user->hasRole('staff'))->toBeTrue();
});

test('storing a user validates required email, duplicate email, and password confirmation', function () {
    $this->from(route('users.create'))
        ->post(route('users.store'), [
            'name' => 'Test User',
            'email' => '',
            'password' => 'pass123',
            'password_confirmation' => 'mismatch',
        ])
        ->assertRedirect(route('users.create'))
        ->assertSessionHasErrors(['email', 'password']);
});

test('users show page displays user details, assigned roles, and permissions', function () {
    $user = User::factory()->create([
        'name' => 'Siti Viewer',
        'email' => 'siti@example.com',
    ]);
    $user->assignRole('user');

    $this->get(route('users.show', $user))
        ->assertOk()
        ->assertViewIs('modules.users.show')
        ->assertSee('Detail Pengguna')
        ->assertSee('Siti Viewer')
        ->assertSee('siti@example.com')
        ->assertSee('user')
        ->assertSee('dashboard.view');
});

test('users edit page displays user information with preselected roles', function () {
    $user = User::factory()->create([
        'name' => 'Ahmad Staff',
        'email' => 'ahmad@example.com',
    ]);
    $user->assignRole('staff');

    $this->get(route('users.edit', $user))
        ->assertOk()
        ->assertViewIs('modules.users.edit')
        ->assertSee('Ubah Pengguna')
        ->assertSee('value="Ahmad Staff"', false)
        ->assertSee('value="ahmad@example.com"', false)
        ->assertSee('Kosongkan jika tidak ingin mengubah kata sandi');
});

test('can update a user and update their password when provided', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    $this->put(route('users.update', $user), [
        'name' => 'New Name',
        'email' => 'new@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
        'roles' => ['user'],
    ])
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success', 'Pengguna berhasil diperbarui.');

    $user->refresh();
    expect($user->name)->toBe('New Name')
        ->and($user->email)->toBe('new@example.com')
        ->and(Hash::check('newpassword123', $user->password))->toBeTrue()
        ->and($user->hasRole('user'))->toBeTrue();
});

test('updating user without password retains original password', function () {
    $user = User::factory()->create([
        'name' => 'Same Pass User',
        'email' => 'samepass@example.com',
        'password' => Hash::make('original-password'),
    ]);

    $this->put(route('users.update', $user), [
        'name' => 'Same Pass User Updated',
        'email' => 'samepass@example.com',
        'password' => '',
        'password_confirmation' => '',
    ])
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success', 'Pengguna berhasil diperbarui.');

    $user->refresh();
    expect(Hash::check('original-password', $user->password))->toBeTrue();
});

test('cannot delete currently authenticated user', function () {
    $admin = auth()->user();

    $this->delete(route('users.destroy', $admin))
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('error', 'Anda tidak dapat menghapus akun Anda sendiri.');

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

test('can delete another user successfully', function () {
    $user = User::factory()->create();

    $this->delete(route('users.destroy', $user))
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success', 'Pengguna berhasil dihapus.');

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('unauthenticated guest cannot access user management', function () {
    auth()->logout();

    $this->get(route('users.index'))->assertRedirect('/auth/login');
    $this->get(route('users.create'))->assertRedirect('/auth/login');
    $this->post(route('users.store'), [])->assertRedirect('/auth/login');
});

test('user without users.view permission receives forbidden 403', function () {
    $nonAdmin = User::factory()->create(['role' => 'user']);
    $role = Role::firstOrCreate(['name' => 'viewer_only', 'guard_name' => 'web']);
    $nonAdmin->assignRole($role);

    $this->actingAs($nonAdmin);

    $this->get(route('users.index'))->assertForbidden();
    $this->get(route('users.create'))->assertForbidden();
});
