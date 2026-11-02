<?php

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('user seeder creates an admin user', function () {
    $this->seed(UserSeeder::class);

    $admin = User::query()->where('email', 'admin@example.com')->first();

    expect($admin)->not->toBeNull()
        ->and($admin->role)->toBe('admin')
        ->and(Hash::check('password', $admin->password))->toBeTrue();
});

test('login page renders successfully at /auth/login', function () {
    $this->get('/auth/login')
        ->assertOk()
        ->assertViewIs('auth.login')
        ->assertSee('Masuk')
        ->assertSee('name="email"', false)
        ->assertSee('name="password"', false);
});

test('admin user can log in with valid credentials', function () {
    $this->seed(UserSeeder::class);

    $this->post('/auth/login', [
        'email' => 'admin@example.com',
        'password' => 'password',
    ])
        ->assertRedirect(route('classes.index'))
        ->assertSessionHas('success', 'Berhasil masuk.');

    $this->assertAuthenticated();
});

test('login fails with invalid credentials and displays error message', function () {
    $this->seed(UserSeeder::class);

    $this->from('/auth/login')
        ->post('/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ])
        ->assertRedirect('/auth/login')
        ->assertSessionHasErrors([
            'email' => 'Identitas tersebut tidak cocok dengan data kami.',
        ]);

    $this->assertGuest();
});

test('login validates required email and password', function () {
    $this->from('/auth/login')
        ->post('/auth/login', [
            'email' => '',
            'password' => '',
        ])
        ->assertRedirect('/auth/login')
        ->assertSessionHasErrors([
            'email' => 'Kolom Email wajib diisi.',
            'password' => 'Kolom Kata Sandi wajib diisi.',
        ]);
});

test('authenticated user can log out', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
