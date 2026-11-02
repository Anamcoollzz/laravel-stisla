<?php

use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('students index page displays students with their class in datatable and marks sidebar active', function () {
    $schoolClass = SchoolClass::factory()->create([
        'class_name' => 'Kelas 10-IPA-1',
    ]);
    $student = Student::factory()->for($schoolClass, 'schoolClass')->create([
        'nis' => '2026000001',
        'name' => 'Andi Pratama',
        'email' => 'andi@student.test',
        'gender' => 'L',
    ]);

    $this->get(route('students.index'))
        ->assertOk()
        ->assertViewIs('modules.students.index')
        ->assertSee('id="table-students"', false)
        ->assertSee('Daftar Siswa')
        ->assertSee('Tambah Baru')
        ->assertSee('2026000001')
        ->assertSee('Andi Pratama')
        ->assertSee('Kelas 10-IPA-1')
        ->assertSee('Laki-laki')
        ->assertSee('data-toggle="tooltip" title="Detail"><i class="fas fa-eye"></i>', false)
        ->assertSee('data-toggle="tooltip" title="Ubah"><i class="fas fa-pencil-alt"></i>', false)
        ->assertSee('data-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i>', false)
        ->assertSee('<li class="active"><a class="nav-link" href="'.route('students.index').'">Siswa</a></li>', false);
});

test('students index page falls back to english when locale is en', function () {
    app()->setLocale('en');

    $this->get(route('students.index'))
        ->assertOk()
        ->assertSee('Student List')
        ->assertSee('Add New')
        ->assertSee('<li class="active"><a class="nav-link" href="'.route('students.index').'">Students</a></li>', false);
});

test('students create page renders form with class options', function () {
    $schoolClass = SchoolClass::factory()->create([
        'class_name' => 'Kelas 11-IPS-1',
    ]);

    $this->get(route('students.create'))
        ->assertOk()
        ->assertViewIs('modules.students.create')
        ->assertSee('Tambah Siswa')
        ->assertSee('Kelas 11-IPS-1')
        ->assertSee('name="class_id"', false)
        ->assertSee('name="nis"', false)
        ->assertSee('name="name"', false);
});

test('can store a new student belonging to a class and flash swal success message', function () {
    $schoolClass = SchoolClass::factory()->create();

    $payload = [
        'class_id' => $schoolClass->id,
        'nis' => '2026000099',
        'name' => 'Putri Maharani',
        'email' => 'putri@student.test',
        'phone' => '081234567899',
        'gender' => 'P',
        'birth_date' => '2010-05-15',
        'address' => 'Jl. Pelajar No. 12',
    ];

    $this->post(route('students.store'), $payload)
        ->assertRedirect(route('students.index'))
        ->assertSessionHas('success', 'Siswa berhasil ditambahkan.');

    $this->assertDatabaseHas('students', [
        'class_id' => $schoolClass->id,
        'nis' => '2026000099',
        'name' => 'Putri Maharani',
        'email' => 'putri@student.test',
    ]);

    $this->get(route('students.index'))
        ->assertOk()
        ->assertSee('swal("Berhasil", "Siswa berhasil ditambahkan.", \'success\');', false);
});

test('storing a student validates required fields, class relationship, and unique constraints with swal error', function (array $overrides, string $errorField, string $expectedMessage) {
    $schoolClass = SchoolClass::factory()->create();
    Student::factory()->for($schoolClass, 'schoolClass')->create([
        'nis' => '2026000010',
        'email' => 'existing@student.test',
    ]);

    $payload = array_merge([
        'class_id' => $schoolClass->id,
        'nis' => '2026000011',
        'name' => 'Bima Sakti',
        'email' => 'bima@student.test',
        'phone' => '081299998888',
        'gender' => 'L',
        'birth_date' => '2010-01-01',
        'address' => 'Jl. Pemuda No. 3',
    ], $overrides);

    $this->from(route('students.create'))
        ->post(route('students.store'), $payload)
        ->assertRedirect(route('students.create'))
        ->assertSessionHasErrors([
            $errorField => $expectedMessage,
        ]);

    $this->followRedirects($this->from(route('students.create'))->post(route('students.store'), $payload))
        ->assertOk()
        ->assertSee('swal("Gagal",', false)
        ->assertSee($expectedMessage);
})->with([
    'missing class' => [['class_id' => ''], 'class_id', 'Kolom Kelas wajib diisi.'],
    'nonexistent class' => [['class_id' => 99999], 'class_id', 'Kelas yang dipilih tidak valid.'],
    'missing nis' => [['nis' => ''], 'nis', 'Kolom NIS wajib diisi.'],
    'duplicate nis' => [['nis' => '2026000010'], 'nis', 'NIS sudah digunakan.'],
    'missing name' => [['name' => ''], 'name', 'Kolom Nama Siswa wajib diisi.'],
    'invalid email' => [['email' => 'invalid-email'], 'email', 'Kolom Email harus berupa alamat surel yang valid.'],
    'duplicate email' => [['email' => 'existing@student.test'], 'email', 'Email sudah digunakan.'],
    'invalid gender' => [['gender' => 'X'], 'gender', 'Jenis Kelamin yang dipilih tidak valid.'],
]);

test('students show page displays student details and icon back button with tooltip', function () {
    $schoolClass = SchoolClass::factory()->create([
        'class_name' => 'Kelas 12-IPA-2',
    ]);
    $student = Student::factory()->for($schoolClass, 'schoolClass')->create([
        'name' => 'Citra Kirana',
    ]);

    $this->get(route('students.show', $student))
        ->assertOk()
        ->assertViewIs('modules.students.show')
        ->assertSee('Detail Siswa')
        ->assertSee('Citra Kirana')
        ->assertSee('Kelas 12-IPA-2')
        ->assertSee('data-toggle="tooltip" title="Kembali"><i class="fas fa-arrow-left"></i>', false)
        ->assertSee('data-toggle="tooltip" title="Ubah"><i class="fas fa-pencil-alt"></i>', false);
});

test('students edit page displays existing student', function () {
    $student = Student::factory()->create([
        'name' => 'Dimas Anggara',
    ]);

    $this->get(route('students.edit', $student))
        ->assertOk()
        ->assertViewIs('modules.students.edit')
        ->assertSee('Ubah Siswa')
        ->assertSee('value="Dimas Anggara"', false);
});

test('can update a student and change class while keeping own nis and email', function () {
    $oldClass = SchoolClass::factory()->create();
    $newClass = SchoolClass::factory()->create();
    $student = Student::factory()->for($oldClass, 'schoolClass')->create([
        'nis' => '2026000077',
        'name' => 'Eka Putri',
        'email' => 'eka@student.test',
        'gender' => 'P',
    ]);

    $this->put(route('students.update', $student), [
        'class_id' => $newClass->id,
        'nis' => '2026000077',
        'name' => 'Eka Putri Updated',
        'email' => 'eka@student.test',
        'phone' => '081111222333',
        'gender' => 'P',
        'birth_date' => '2011-08-17',
        'address' => 'Jl. Baru No. 9',
    ])
        ->assertRedirect(route('students.index'))
        ->assertSessionHas('success', 'Siswa berhasil diperbarui.');

    $student->refresh();
    expect($student->name)->toBe('Eka Putri Updated')
        ->and($student->class_id)->toBe($newClass->id);
});

test('updating a student rejects duplicate nis or email from another student', function () {
    Student::factory()->create([
        'nis' => '2026000055',
        'email' => 'taken@student.test',
    ]);
    $student = Student::factory()->create();

    $this->from(route('students.edit', $student))
        ->put(route('students.update', $student), [
            'class_id' => $student->class_id,
            'nis' => '2026000055',
            'name' => $student->name,
            'email' => 'taken@student.test',
            'gender' => $student->gender,
        ])
        ->assertRedirect(route('students.edit', $student))
        ->assertSessionHasErrors([
            'nis' => 'NIS sudah digunakan.',
            'email' => 'Email sudah digunakan.',
        ]);
});

test('can delete a student and deleting a class cascades to its students', function () {
    $schoolClass = SchoolClass::factory()->create();
    $student = Student::factory()->for($schoolClass, 'schoolClass')->create();

    $this->delete(route('students.destroy', $student))
        ->assertRedirect(route('students.index'))
        ->assertSessionHas('success', 'Siswa berhasil dihapus.');

    $this->assertDatabaseMissing('students', [
        'id' => $student->id,
    ]);

    $cascadedStudent = Student::factory()->for($schoolClass, 'schoolClass')->create();
    $schoolClass->delete();

    $this->assertDatabaseMissing('students', [
        'id' => $cascadedStudent->id,
    ]);
});
