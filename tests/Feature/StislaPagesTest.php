<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->actingAsAdmin());

/**
 * @return array<int, string>
 */
function stislaPageRouteNames(): array
{
    return collect(Route::getRoutes()->getRoutesByName())
        ->keys()
        ->filter(fn (string $name): bool => Str::endsWith($name, '.html'))
        ->values()
        ->all();
}

test('every stisla page renders successfully', function () {
    $routeNames = stislaPageRouteNames();

    expect($routeNames)->not->toBeEmpty();

    foreach ($routeNames as $routeName) {
        $this->get(route($routeName))->assertOk();
    }
});

test('every local asset referenced by stisla pages exists in public', function () {
    $baseUrl = rtrim(asset(''), '/');

    foreach (stislaPageRouteNames() as $routeName) {
        $html = $this->get(route($routeName))->getContent();

        preg_match_all('#(?:src|href)="'.preg_quote($baseUrl, '#').'/((?:assets|node_modules)/[^"?\#]+)"#', $html, $matches);

        foreach (array_unique($matches[1]) as $path) {
            expect(public_path($path))->toBeFile("Missing [{$path}] on [{$routeName}]");
        }
    }
});

test('the sidebar marks the current page as active when stisla demo is enabled', function () {
    config()->set('app.is_stisla_demo', true);

    $this->get(route('bootstrap-alert.html'))
        ->assertOk()
        ->assertSee('<li class="active"><a class="nav-link" href="'.route('bootstrap-alert.html').'">Alert</a></li>', false)
        ->assertSee('<li class="nav-item dropdown active">', false);
});

test('the sidebar only shows crud menu when stisla demo is disabled', function () {
    config()->set('app.is_stisla_demo', false);

    $this->get(route('index.html'))
        ->assertOk()
        ->assertSee(route('classes.index'))
        ->assertSee(route('teachers.index'))
        ->assertSee(route('students.index'))
        ->assertDontSee(route('bootstrap-alert.html'));
});
