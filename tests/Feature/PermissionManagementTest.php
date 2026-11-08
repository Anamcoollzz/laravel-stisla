<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->actingAsAdmin());

test('permissions index page lists permissions and marks sidebar active', function () {
    config()->set('app.is_stisla_demo', false);

    $this->get(route('permissions.index'))
        ->assertOk()
        ->assertViewIs('modules.permissions.index')
        ->assertSee('id="table-permissions"', false)
        ->assertSee('subjects.view')
        ->assertSee('<li class="active"><a class="nav-link" href="'.route('permissions.index').'">', false);
});

test('can store a new permission with the web guard', function () {
    $this->post(route('permissions.store'), [
        'name' => 'reports.view',
    ])
        ->assertRedirect(route('permissions.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('permissions', [
        'name' => 'reports.view',
        'guard_name' => 'web',
    ]);
});

test('storing a permission validates format and uniqueness', function (string $name) {
    $permissionCount = Permission::query()->count();

    $this->from(route('permissions.create'))
        ->post(route('permissions.store'), ['name' => $name])
        ->assertRedirect(route('permissions.create'))
        ->assertSessionHasErrors('name');

    $this->assertDatabaseCount('permissions', $permissionCount);
})->with([
    'missing dot' => ['reportsview'],
    'uppercase' => ['Reports.View'],
    'space' => ['reports view'],
    'existing name' => ['subjects.view'],
]);

test('can rename a permission', function () {
    $permission = Permission::query()->where('name', 'subjects.view')->firstOrFail();

    $this->put(route('permissions.update', $permission), [
        'name' => 'subjects.read',
    ])
        ->assertRedirect(route('permissions.index'))
        ->assertSessionHas('success');

    expect($permission->refresh()->name)->toBe('subjects.read');
});

test('deleting a permission removes it from roles', function () {
    $permission = Permission::query()->create(['name' => 'reports.view', 'guard_name' => 'web']);
    $role = Role::query()->where('name', 'staff')->firstOrFail();
    $role->givePermissionTo($permission);

    $this->delete(route('permissions.destroy', $permission))
        ->assertRedirect(route('permissions.index'));

    $this->assertModelMissing($permission);
    expect($role->fresh()->hasPermissionTo('subjects.view'))->toBeTrue();
    $this->assertDatabaseMissing('role_has_permissions', ['permission_id' => $permission->id]);
});

test('user without permissions.view receives 403 forbidden', function () {
    $this->actingAsUser();

    $this->get(route('permissions.index'))->assertForbidden();
    $this->get(route('permissions.create'))->assertForbidden();
});
