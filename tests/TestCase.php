<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function actingAsAdmin(?User $admin = null): User
    {
        $this->seed(RolePermissionSeeder::class);

        $admin ??= User::factory()->create(['role' => 'admin']);

        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        $this->actingAs($admin);

        return $admin;
    }

    public function actingAsUser(?User $user = null): User
    {
        $this->seed(RolePermissionSeeder::class);

        $user ??= User::factory()->create(['role' => 'user']);

        if (! $user->hasRole('user')) {
            $user->assignRole('user');
        }

        $this->actingAs($user);

        return $user;
    }
}
