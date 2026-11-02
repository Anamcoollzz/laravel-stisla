<?php

use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('teachers index page displays teachers in datatable with indonesian translations and marks sidebar active', function () {
    $teacher = Teacher::factory()->create([
        'nip' => '198501012010011001',
        'name' => 'Budi Santoso, S.Pd.',
        'email' => 'budi@school.test',
        'gender' => 'L',
    ]);

    $this->get(route('teachers.index'))
        ->assertOk()
        ->assertViewIs('modules.teachers.index')
        ->assertSee('id="table-teachers"', false)
        ->assertSee('Daftar Guru')
        ->assertSee('Tambah Baru')
        ->assertSee('198501012010011001')
        ->assertSee('Budi Santoso, S.Pd.')
        ->assertSee('Laki-laki')
        ->assertSee('data-toggle="tooltip" title="Detail"><i class="fas fa-eye"></i>', false)
        ->assertSee('data-toggle="tooltip" title="Ubah"><i class="fas fa-pencil-alt"></i>', false)
        ->assertSee('data-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i>', false)
        ->assertSee('<li class="active"><a class="nav-link" href="'.route('teachers.index').'">Guru</a></li>', false);
});

test('teachers index page falls back to english when locale is en', function () {
    app()->setLocale('en');

    $this->get(route('teachers.index'))
        ->assertOk()
        ->assertSee('Teacher List')
        ->assertSee('Add New')
        ->assertSee('<li class="active"><a class="nav-link" href="'.route('teachers.index').'">Teachers</a></li>', false);
});

test('teachers create page renders form', function () {
    $this->get(route('teachers.create'))
        ->assertOk()
        ->assertViewIs('modules.teachers.create')
        ->assertSee('Tambah Guru')
        ->assertSee('name="nip"', false)
        ->assertSee('name="name"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="phone"', false)
        ->assertSee('name="gender"', false)
        ->assertSee('name="address"', false);
});

test('can store a new teacher and flash swal success message in indonesian', function () {
    $payload = [
        'nip' => '199002022015022002',
        'name' => 'Siti Aminah, M.Pd.',
        'email' => 'siti@school.test',
        'phone' => '081234567890',
        'gender' => 'P',
        'address' => 'Jl. Pendidikan No. 10',
    ];

    $this->post(route('teachers.store'), $payload)
        ->assertRedirect(route('teachers.index'))
        ->assertSessionHas('success', 'Guru berhasil ditambahkan.');

    $this->assertDatabaseHas('teachers', $payload);

    $this->get(route('teachers.index'))
        ->assertOk()
        ->assertSee('swal("Berhasil", "Guru berhasil ditambahkan.", \'success\');', false);
});

test('storing a teacher validates required, email, gender, and unique fields with swal error', function (array $overrides, string $errorField, string $expectedMessage) {
    Teacher::factory()->create([
        'nip' => '198001012005011001',
        'email' => 'existing@school.test',
    ]);

    $payload = array_merge([
        'nip' => '199103032018031003',
        'name' => 'Ahmad Fauzi',
        'email' => 'ahmad@school.test',
        'phone' => '081298765432',
        'gender' => 'L',
        'address' => 'Jl. Merdeka No. 1',
    ], $overrides);

    $this->from(route('teachers.create'))
        ->post(route('teachers.store'), $payload)
        ->assertRedirect(route('teachers.create'))
        ->assertSessionHasErrors([
            $errorField => $expectedMessage,
        ]);

    $this->followRedirects($this->from(route('teachers.create'))->post(route('teachers.store'), $payload))
        ->assertOk()
        ->assertSee('swal("Gagal",', false)
        ->assertSee($expectedMessage);
})->with([
    'missing nip' => [['nip' => ''], 'nip', 'Kolom NIP wajib diisi.'],
    'duplicate nip' => [['nip' => '198001012005011001'], 'nip', 'NIP sudah digunakan.'],
    'missing name' => [['name' => ''], 'name', 'Kolom Nama Guru wajib diisi.'],
    'invalid email' => [['email' => 'not-an-email'], 'email', 'Kolom Email harus berupa alamat surel yang valid.'],
    'duplicate email' => [['email' => 'existing@school.test'], 'email', 'Email sudah digunakan.'],
    'invalid gender' => [['gender' => 'X'], 'gender', 'Jenis Kelamin yang dipilih tidak valid.'],
]);

test('teachers show page displays teacher details', function () {
    $teacher = Teacher::factory()->create([
        'name' => 'Drs. Hendra Wijaya',
    ]);

    $this->get(route('teachers.show', $teacher))
        ->assertOk()
        ->assertViewIs('modules.teachers.show')
        ->assertSee('Detail Guru')
        ->assertSee('Drs. Hendra Wijaya');
});

test('teachers edit page displays existing teacher', function () {
    $teacher = Teacher::factory()->create([
        'name' => 'Rina Kartika, S.Pd.',
    ]);

    $this->get(route('teachers.edit', $teacher))
        ->assertOk()
        ->assertViewIs('modules.teachers.edit')
        ->assertSee('Ubah Guru')
        ->assertSee('value="Rina Kartika, S.Pd."', false);
});

test('can update a teacher and allow keeping own nip and email', function () {
    $teacher = Teacher::factory()->create([
        'nip' => '198707072012072007',
        'name' => 'Dewi Lestari',
        'email' => 'dewi@school.test',
        'gender' => 'P',
    ]);

    $this->put(route('teachers.update', $teacher), [
        'nip' => '198707072012072007',
        'name' => 'Dewi Lestari, S.Pd.',
        'email' => 'dewi@school.test',
        'phone' => '081122334455',
        'gender' => 'P',
        'address' => 'Jl. Baru No. 5',
    ])
        ->assertRedirect(route('teachers.index'))
        ->assertSessionHas('success', 'Guru berhasil diperbarui.');

    expect($teacher->fresh()->name)->toBe('Dewi Lestari, S.Pd.');
});

test('updating a teacher rejects duplicate nip or email from another record', function () {
    Teacher::factory()->create([
        'nip' => '197505052000051005',
        'email' => 'taken@school.test',
    ]);
    $teacher = Teacher::factory()->create();

    $this->from(route('teachers.edit', $teacher))
        ->put(route('teachers.update', $teacher), [
            'nip' => '197505052000051005',
            'name' => $teacher->name,
            'email' => 'taken@school.test',
            'gender' => $teacher->gender,
        ])
        ->assertRedirect(route('teachers.edit', $teacher))
        ->assertSessionHasErrors([
            'nip' => 'NIP sudah digunakan.',
            'email' => 'Email sudah digunakan.',
        ]);
});

test('can delete a teacher', function () {
    $teacher = Teacher::factory()->create();

    $this->delete(route('teachers.destroy', $teacher))
        ->assertRedirect(route('teachers.index'))
        ->assertSessionHas('success', 'Guru berhasil dihapus.');

    $this->assertDatabaseMissing('teachers', [
        'id' => $teacher->id,
    ]);
});
