<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->actingAsAdmin());

test('roles index page displays roles in datatable with users and permissions count', function () {
    $this->get(route('roles.index'))
        ->assertOk()
        ->assertViewIs('modules.roles.index')
        ->assertSee('id="table-roles"', false)
        ->assertSee('Daftar Peran')
        ->assertSee('Tambah Baru')
        ->assertSee('admin')
        ->assertSee('staff')
        ->assertSee('user');
});

test('roles create page renders form with module permission groups', function () {
    $this->get(route('roles.create'))
        ->assertOk()
        ->assertViewIs('modules.roles.create')
        ->assertSee('Tambah Peran')
        ->assertSee('name="name"', false)
        ->assertSee('name="permissions[]"', false)
        ->assertSee('classes.view')
        ->assertSee('users.view');
});

test('can store a new role and sync permissions', function () {
    $this->post(route('roles.store'), [
        'name' => 'finance_manager',
        'permissions' => ['spp-payments.view', 'spp-payments.create', 'spp-payments.edit'],
    ])
        ->assertRedirect(route('roles.index'))
        ->assertSessionHas('success', 'Peran berhasil ditambahkan.');

    $role = Role::where('name', 'finance_manager')->first();
    expect($role)->not->toBeNull()
        ->and($role->hasPermissionTo('spp-payments.view'))->toBeTrue()
        ->and($role->hasPermissionTo('spp-payments.create'))->toBeTrue()
        ->and($role->hasPermissionTo('classes.view'))->toBeFalse();
});

test('storing a role validates unique role name', function () {
    $this->from(route('roles.create'))
        ->post(route('roles.store'), [
            'name' => 'admin',
        ])
        ->assertRedirect(route('roles.create'))
        ->assertSessionHasErrors(['name']);
});

test('roles show page displays role details and assigned permissions', function () {
    $role = Role::where('name', 'staff')->first();

    $this->get(route('roles.show', $role))
        ->assertOk()
        ->assertViewIs('modules.roles.show')
        ->assertSee('Detail Peran')
        ->assertSee('staff')
        ->assertSee('classes.view')
        ->assertSee('teachers.view');
});

test('roles edit page renders form with assigned permissions checked', function () {
    $role = Role::where('name', 'user')->first();

    $this->get(route('roles.edit', $role))
        ->assertOk()
        ->assertViewIs('modules.roles.edit')
        ->assertSee('Ubah Peran')
        ->assertSee('value="user"', false);
});

test('can update role name and sync updated permissions', function () {
    $role = Role::create(['name' => 'custom_role', 'guard_name' => 'web']);
    $role->syncPermissions(['classes.view']);

    $this->put(route('roles.update', $role), [
        'name' => 'custom_role_updated',
        'permissions' => ['classes.view', 'classes.create'],
    ])
        ->assertRedirect(route('roles.index'))
        ->assertSessionHas('success', 'Peran berhasil diperbarui.');

    $role->refresh();
    expect($role->name)->toBe('custom_role_updated')
        ->and($role->hasPermissionTo('classes.create'))->toBeTrue();
});

test('cannot delete default admin role', function () {
    $adminRole = Role::where('name', 'admin')->first();

    $this->delete(route('roles.destroy', $adminRole))
        ->assertRedirect(route('roles.index'))
        ->assertSessionHas('error', 'Peran admin tidak dapat dihapus.');

    $this->assertDatabaseHas('roles', ['name' => 'admin']);
});

test('can delete a custom role', function () {
    $role = Role::create(['name' => 'temporary_role', 'guard_name' => 'web']);

    $this->delete(route('roles.destroy', $role))
        ->assertRedirect(route('roles.index'))
        ->assertSessionHas('success', 'Peran berhasil dihapus.');

    $this->assertDatabaseMissing('roles', ['id' => $role->id]);
});

test('unauthenticated guest cannot access role management', function () {
    auth()->logout();

    $this->get(route('roles.index'))->assertRedirect('/auth/login');
    $this->get(route('roles.create'))->assertRedirect('/auth/login');
});

test('user without roles.view permission receives 403 forbidden', function () {
    $user = User::factory()->create();
    $role = Role::create(['name' => 'limited', 'guard_name' => 'web']);
    $user->assignRole($role);

    $this->actingAs($user);

    $this->get(route('roles.index'))->assertForbidden();
    $this->get(route('roles.create'))->assertForbidden();
});

test('sidebar respects module and access control permissions', function () {
    // 1. Admin sees everything
    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee(route('classes.index'))
        ->assertSee(route('teachers.index'))
        ->assertSee(route('students.index'))
        ->assertSee(route('subjects.index'))
        ->assertSee(route('spp-payments.index'))
        ->assertSee(route('users.index'))
        ->assertSee(route('roles.index'));

    // 2. User with only classes.view sees only classes, not other modules or user management
    $restrictedUser = User::factory()->create();
    $restrictedRole = Role::create(['name' => 'classes_only', 'guard_name' => 'web']);
    $restrictedRole->syncPermissions(['dashboard.view', 'classes.view']);
    $restrictedUser->assignRole($restrictedRole);

    $this->actingAs($restrictedUser);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee(route('classes.index'))
        ->assertDontSee(route('teachers.index'))
        ->assertDontSee(route('students.index'))
        ->assertDontSee(route('subjects.index'))
        ->assertDontSee(route('spp-payments.index'))
        ->assertDontSee(route('users.index'))
        ->assertDontSee(route('roles.index'));

    // Trying to access teachers.index returns 403
    $this->get(route('teachers.index'))->assertForbidden();
    $this->post(route('classes.store'), ['class_name' => '10-A'])->assertForbidden();
});
