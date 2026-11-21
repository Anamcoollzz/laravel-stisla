<?php

use App\Models\ActivityLog;
use App\Models\User;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(MenuSeeder::class);
});

test('guests are redirected to login when accessing file manager', function () {
    $this->get(route('file-manager.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access file manager', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('file-manager.index'))
        ->assertForbidden();
});

test('admin can view public and private storage directories', function () {
    Storage::fake('public');
    Storage::fake('local');

    Storage::disk('public')->put('docs/readme.txt', 'Public content');
    Storage::disk('local')->put('secrets/token.txt', 'Private content');

    $this->actingAsAdmin();

    $this->get(route('file-manager.index', ['disk' => 'public']))
        ->assertOk()
        ->assertViewIs('modules.file-manager.index')
        ->assertSee('docs');

    $this->get(route('file-manager.index', ['disk' => 'private', 'path' => 'secrets']))
        ->assertOk()
        ->assertSee('token.txt');
});

test('admin can create folder, upload file, rename, edit content, download, and delete on both public and private storage', function (string $visibility, string $diskName) {
    Storage::fake($diskName);
    $this->actingAsAdmin();

    // Create folder
    $this->post(route('file-manager.folder.store'), [
        'disk' => $visibility,
        'path' => '',
        'name' => 'invoices',
    ])
        ->assertRedirect(route('file-manager.index', ['disk' => $visibility, 'path' => '']))
        ->assertSessionHas('success');

    expect(Storage::disk($diskName)->directoryExists('invoices'))->toBeTrue();

    // Upload file
    $upload = UploadedFile::fake()->create('report.pdf', 120, 'application/pdf');
    $this->post(route('file-manager.upload'), [
        'disk' => $visibility,
        'path' => 'invoices',
        'files' => [$upload],
    ])->assertSessionHas('success');

    expect(Storage::disk($diskName)->fileExists('invoices/report.pdf'))->toBeTrue();

    // Create editable text file
    $this->post(route('file-manager.file.store'), [
        'disk' => $visibility,
        'path' => 'invoices',
        'name' => 'notes.txt',
        'content' => 'Initial note',
    ])->assertSessionHas('success');

    expect(Storage::disk($diskName)->get('invoices/notes.txt'))->toBe('Initial note');

    // Edit text file
    $this->get(route('file-manager.edit', ['disk' => $visibility, 'target_path' => 'invoices/notes.txt']))
        ->assertOk()
        ->assertSee('Initial note');

    $this->put(route('file-manager.content.update'), [
        'disk' => $visibility,
        'target_path' => 'invoices/notes.txt',
        'content' => 'Updated note',
    ])->assertSessionHas('success');

    expect(Storage::disk($diskName)->get('invoices/notes.txt'))->toBe('Updated note');

    // Rename file
    $this->patch(route('file-manager.rename'), [
        'disk' => $visibility,
        'path' => 'invoices',
        'target_path' => 'invoices/notes.txt',
        'new_name' => 'summary.txt',
    ])->assertSessionHas('success');

    expect(Storage::disk($diskName)->fileExists('invoices/notes.txt'))->toBeFalse();
    expect(Storage::disk($diskName)->fileExists('invoices/summary.txt'))->toBeTrue();

    // Download file
    $this->get(route('file-manager.download', ['disk' => $visibility, 'target_path' => 'invoices/summary.txt']))
        ->assertOk();

    // Delete file
    $this->delete(route('file-manager.destroy'), [
        'disk' => $visibility,
        'path' => 'invoices',
        'target_path' => 'invoices/summary.txt',
    ])->assertSessionHas('success');

    expect(Storage::disk($diskName)->fileExists('invoices/summary.txt'))->toBeFalse();

    // Delete folder recursively
    $this->delete(route('file-manager.destroy'), [
        'disk' => $visibility,
        'path' => '',
        'target_path' => 'invoices',
    ])->assertSessionHas('success');

    expect(Storage::disk($diskName)->directoryExists('invoices'))->toBeFalse();

    expect(ActivityLog::where('module', 'file-manager')->count())->toBeGreaterThanOrEqual(5);
})->with([
    'public disk' => ['public', 'public'],
    'private disk' => ['private', 'local'],
]);

test('path traversal attempts are rejected', function () {
    Storage::fake('local');
    $this->actingAsAdmin();

    $this->post(route('file-manager.folder.store'), [
        'disk' => 'private',
        'path' => '../../etc',
        'name' => 'hack',
    ])->assertSessionHas('error');
});
