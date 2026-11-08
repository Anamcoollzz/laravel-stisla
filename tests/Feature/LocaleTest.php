<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;

uses(RefreshDatabase::class);

test('user can switch application locale to English', function () {
    $response = $this->get(route('lang.switch', 'en'));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('locale', 'en');
});

test('user can switch application locale to Indonesian', function () {
    $response = $this->withSession(['locale' => 'en'])
        ->get(route('lang.switch', 'id'));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('locale', 'id');
});

test('invalid locale does not update session locale', function () {
    $response = $this->withSession(['locale' => 'id'])
        ->get(route('lang.switch', 'fr'));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionMissing('locale', 'fr');
});

test('set locale middleware applies session locale to application', function () {
    $this->withSession(['locale' => 'en'])
        ->get(route('login'))
        ->assertOk();

    expect(App::getLocale())->toBe('en');

    $this->withSession(['locale' => 'id'])
        ->get(route('login'))
        ->assertOk();

    expect(App::getLocale())->toBe('id');
});

test('navbar displays language switch dropdown', function () {
    $this->actingAsAdmin();

    $response = $this->get(route('dashboard'));

    $response->assertOk()
        ->assertSee(route('lang.switch', 'id'), false)
        ->assertSee(route('lang.switch', 'en'), false)
        ->assertSee('Bahasa Indonesia')
        ->assertSee('English');
});
