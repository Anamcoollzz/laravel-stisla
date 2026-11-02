<?php

use App\Models\SchoolClass;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('classes index page displays classes in datatable with indonesian translations and marks sidebar active', function () {
    $schoolClass = SchoolClass::factory()->create([
        'class_name' => 'Kelas 10-A',
    ]);

    $this->get(route('classes.index'))
        ->assertOk()
        ->assertViewIs('modules.classes.index')
        ->assertSee('id="table-classes"', false)
        ->assertSee('Daftar Kelas')
        ->assertSee('Tambah Baru')
        ->assertSee('Kelas 10-A')
        ->assertSee('data-toggle="tooltip" title="Detail"><i class="fas fa-eye"></i>', false)
        ->assertSee('data-toggle="tooltip" title="Ubah"><i class="fas fa-pencil-alt"></i>', false)
        ->assertSee('data-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i>', false)
        ->assertSee('<li class="active"><a class="nav-link" href="'.route('classes.index').'">Kelas</a></li>', false);
});

test('classes index page falls back to english when locale is en', function () {
    app()->setLocale('en');

    $this->get(route('classes.index'))
        ->assertOk()
        ->assertSee('Class List')
        ->assertSee('Add New')
        ->assertSee('<li class="active"><a class="nav-link" href="'.route('classes.index').'">Classes</a></li>', false);
});

test('classes create page renders form', function () {
    $this->get(route('classes.create'))
        ->assertOk()
        ->assertViewIs('modules.classes.create')
        ->assertSee('Tambah Kelas')
        ->assertSee('name="class_name"', false);
});

test('can store a new class and flash swal success message in indonesian', function () {
    $this->post(route('classes.store'), [
        'class_name' => 'Kelas 11-IPA-1',
    ])
        ->assertRedirect(route('classes.index'))
        ->assertSessionHas('success', 'Kelas berhasil ditambahkan.');

    $this->assertDatabaseHas('classes', [
        'class_name' => 'Kelas 11-IPA-1',
    ]);

    $this->get(route('classes.index'))
        ->assertOk()
        ->assertSee('swal("Berhasil", "Kelas berhasil ditambahkan.", \'success\');', false);
});

test('storing a class requires a valid unique class_name and renders swal error', function (string $invalidValue, string $expectedMessage) {
    SchoolClass::factory()->create([
        'class_name' => 'Kelas 12-A',
    ]);

    $this->from(route('classes.create'))
        ->post(route('classes.store'), [
            'class_name' => $invalidValue,
        ])
        ->assertRedirect(route('classes.create'))
        ->assertSessionHasErrors([
            'class_name' => $expectedMessage,
        ]);

    $this->followRedirects($this->from(route('classes.create'))->post(route('classes.store'), [
        'class_name' => $invalidValue,
    ]))
        ->assertOk()
        ->assertSee('swal("Gagal",', false)
        ->assertSee($expectedMessage);
})->with([
    'empty' => ['', 'Kolom Nama Kelas wajib diisi.'],
    'too long' => [str_repeat('A', 256), 'Kolom Nama Kelas tidak boleh lebih dari 255 karakter.'],
    'duplicate' => ['Kelas 12-A', 'Nama Kelas sudah digunakan.'],
]);

test('classes show page displays class details', function () {
    $schoolClass = SchoolClass::factory()->create([
        'class_name' => 'Kelas 10-B',
    ]);

    $this->get(route('classes.show', $schoolClass))
        ->assertOk()
        ->assertViewIs('modules.classes.show')
        ->assertSee('Detail Kelas')
        ->assertSee('Kelas 10-B');
});

test('classes edit page displays existing class', function () {
    $schoolClass = SchoolClass::factory()->create([
        'class_name' => 'Kelas 10-C',
    ]);

    $this->get(route('classes.edit', $schoolClass))
        ->assertOk()
        ->assertViewIs('modules.classes.edit')
        ->assertSee('Ubah Kelas')
        ->assertSee('value="Kelas 10-C"', false);
});

test('can update a class and allow keeping its own class_name', function () {
    $schoolClass = SchoolClass::factory()->create([
        'class_name' => 'Kelas 10-D',
    ]);

    $this->put(route('classes.update', $schoolClass), [
        'class_name' => 'Kelas 10-D Updated',
    ])
        ->assertRedirect(route('classes.index'))
        ->assertSessionHas('success', 'Kelas berhasil diperbarui.');

    expect($schoolClass->fresh()->class_name)->toBe('Kelas 10-D Updated');

    $this->put(route('classes.update', $schoolClass), [
        'class_name' => 'Kelas 10-D Updated',
    ])
        ->assertRedirect(route('classes.index'))
        ->assertSessionHasNoErrors();
});

test('updating a class rejects duplicate class_name from another record', function () {
    SchoolClass::factory()->create([
        'class_name' => 'Kelas Existing',
    ]);
    $schoolClass = SchoolClass::factory()->create([
        'class_name' => 'Kelas Target',
    ]);

    $this->from(route('classes.edit', $schoolClass))
        ->put(route('classes.update', $schoolClass), [
            'class_name' => 'Kelas Existing',
        ])
        ->assertRedirect(route('classes.edit', $schoolClass))
        ->assertSessionHasErrors([
            'class_name' => 'Nama Kelas sudah digunakan.',
        ]);
});

test('can delete a class', function () {
    $schoolClass = SchoolClass::factory()->create();

    $this->delete(route('classes.destroy', $schoolClass))
        ->assertRedirect(route('classes.index'))
        ->assertSessionHas('success', 'Kelas berhasil dihapus.');

    $this->assertDatabaseMissing('classes', [
        'id' => $schoolClass->id,
    ]);
});
