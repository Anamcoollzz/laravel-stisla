<?php

use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->actingAsAdmin());

test('subjects index page displays subjects in datatable with indonesian translations and marks sidebar active', function () {
    $subject = Subject::factory()->create([
        'code' => 'MP-001',
        'name' => 'Matematika Wajib',
        'description' => 'Matematika dasar untuk kelas 10',
    ]);

    $this->get(route('subjects.index'))
        ->assertOk()
        ->assertViewIs('modules.subjects.index')
        ->assertSee('id="table-subjects"', false)
        ->assertSee('Daftar Mata Pelajaran')
        ->assertSee('Tambah Baru')
        ->assertSee('MP-001')
        ->assertSee('Matematika Wajib')
        ->assertSee('data-toggle="tooltip" title="Detail"><i class="fas fa-eye"></i>', false)
        ->assertSee('data-toggle="tooltip" title="Ubah"><i class="fas fa-pencil-alt"></i>', false)
        ->assertSee('data-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i>', false)
        ->assertSee('<li class="active"><a class="nav-link" href="'.route('subjects.index').'">Mata Pelajaran</a></li>', false);
});

test('subjects index page falls back to english when locale is en', function () {
    app()->setLocale('en');

    $this->get(route('subjects.index'))
        ->assertOk()
        ->assertSee('Subject List')
        ->assertSee('Add New')
        ->assertSee('<li class="active"><a class="nav-link" href="'.route('subjects.index').'">Subjects</a></li>', false);
});

test('subjects create page renders form with required asterisks', function () {
    $this->get(route('subjects.create'))
        ->assertOk()
        ->assertViewIs('modules.subjects.create')
        ->assertSee('Tambah Mata Pelajaran')
        ->assertSee('name="code"', false)
        ->assertSee('name="name"', false)
        ->assertSee('name="description"', false);
});

test('can store a new subject and flash swal success message in indonesian', function () {
    $payload = [
        'code' => 'MP-101',
        'name' => 'Fisika',
        'description' => 'Pelajaran Fisika Dasar',
    ];

    $this->post(route('subjects.store'), $payload)
        ->assertRedirect(route('subjects.index'))
        ->assertSessionHas('success', 'Mata pelajaran berhasil ditambahkan.');

    $this->assertDatabaseHas('subjects', $payload);

    $this->get(route('subjects.index'))
        ->assertOk()
        ->assertSee('swal("Berhasil", "Mata pelajaran berhasil ditambahkan.", \'success\');', false);
});

test('storing a subject validates required and unique fields with swal error', function (array $overrides, string $errorField, string $expectedMessage) {
    Subject::factory()->create([
        'code' => 'MP-EXISTING',
    ]);

    $payload = array_merge([
        'code' => 'MP-202',
        'name' => 'Biologi',
        'description' => null,
    ], $overrides);

    $this->from(route('subjects.create'))
        ->post(route('subjects.store'), $payload)
        ->assertRedirect(route('subjects.create'))
        ->assertSessionHasErrors([
            $errorField => $expectedMessage,
        ]);

    $this->followRedirects($this->from(route('subjects.create'))->post(route('subjects.store'), $payload))
        ->assertOk()
        ->assertSee('swal("Gagal",', false)
        ->assertSee($expectedMessage);
})->with([
    'missing code' => [['code' => ''], 'code', 'Kolom Kode Mata Pelajaran wajib diisi.'],
    'duplicate code' => [['code' => 'MP-EXISTING'], 'code', 'Kode Mata Pelajaran sudah digunakan.'],
    'missing name' => [['name' => ''], 'name', 'Kolom Nama Mata Pelajaran wajib diisi.'],
]);

test('subjects show page displays subject details and icon back button with tooltip', function () {
    $subject = Subject::factory()->create([
        'code' => 'MP-303',
        'name' => 'Kimia',
    ]);

    $this->get(route('subjects.show', $subject))
        ->assertOk()
        ->assertViewIs('modules.subjects.show')
        ->assertSee('Detail Mata Pelajaran')
        ->assertSee('MP-303')
        ->assertSee('Kimia')
        ->assertSee('data-toggle="tooltip" title="Kembali"><i class="fas fa-arrow-left"></i>', false)
        ->assertSee('data-toggle="tooltip" title="Ubah"><i class="fas fa-pencil-alt"></i>', false);
});

test('subjects edit page displays existing subject', function () {
    $subject = Subject::factory()->create([
        'name' => 'Sejarah Indonesia',
    ]);

    $this->get(route('subjects.edit', $subject))
        ->assertOk()
        ->assertViewIs('modules.subjects.edit')
        ->assertSee('Ubah Mata Pelajaran')
        ->assertSee('value="Sejarah Indonesia"', false);
});

test('can update a subject and allow keeping own code', function () {
    $subject = Subject::factory()->create([
        'code' => 'MP-404',
        'name' => 'Bahasa Inggris',
    ]);

    $this->put(route('subjects.update', $subject), [
        'code' => 'MP-404',
        'name' => 'Bahasa Inggris Lanjutan',
        'description' => 'Updated description',
    ])
        ->assertRedirect(route('subjects.index'))
        ->assertSessionHas('success', 'Mata pelajaran berhasil diperbarui.');

    expect($subject->fresh()->name)->toBe('Bahasa Inggris Lanjutan');
});

test('updating a subject rejects duplicate code from another subject', function () {
    Subject::factory()->create([
        'code' => 'MP-TAKEN',
    ]);
    $subject = Subject::factory()->create();

    $this->from(route('subjects.edit', $subject))
        ->put(route('subjects.update', $subject), [
            'code' => 'MP-TAKEN',
            'name' => $subject->name,
        ])
        ->assertRedirect(route('subjects.edit', $subject))
        ->assertSessionHasErrors([
            'code' => 'Kode Mata Pelajaran sudah digunakan.',
        ]);
});

test('can delete a subject', function () {
    $subject = Subject::factory()->create();

    $this->delete(route('subjects.destroy', $subject))
        ->assertRedirect(route('subjects.index'))
        ->assertSessionHas('success', 'Mata pelajaran berhasil dihapus.');

    $this->assertDatabaseMissing('subjects', [
        'id' => $subject->id,
    ]);
});
