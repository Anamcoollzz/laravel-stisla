<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'role' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        $buyer = User::query()->updateOrCreate(
            ['email' => 'buyer@example.com'],
            [
                'name' => 'Buyer Tester',
                'first_name' => 'Buyer',
                'last_name' => 'Tester',
                'role' => 'user',
                'phone' => '08123456789',
                'address' => 'Jl. Jendral Sudirman No. 123',
                'city' => 'Jakarta Pusat',
                'state' => 'DKI Jakarta',
                'postal_code' => '10220',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        if (! $buyer->hasRole('user')) {
            $buyer->assignRole('user');
        }
    }
}
