<?php

use App\Models\SchoolClass;
use App\Models\SppPayment;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('spp payments index page displays payments with student and class in datatable and marks sidebar active', function () {
    $schoolClass = SchoolClass::factory()->create([
        'class_name' => 'Kelas 10-IPA-1',
    ]);
    $student = Student::factory()->for($schoolClass, 'schoolClass')->create([
        'name' => 'Rizky Pratama',
    ]);
    SppPayment::factory()->for($student)->create([
        'invoice_number' => 'SPP-2026-00001',
        'month' => 7,
        'year' => 2026,
        'amount' => 350000,
        'payment_method' => 'transfer',
        'status' => 'paid',
    ]);

    $this->get(route('spp-payments.index'))
        ->assertOk()
        ->assertViewIs('modules.spp-payments.index')
        ->assertSee('id="table-spp-payments"', false)
        ->assertSee('Daftar Pembayaran SPP')
        ->assertSee('Tambah Baru')
        ->assertSee('SPP-2026-00001')
        ->assertSee('Rizky Pratama')
        ->assertSee('Kelas 10-IPA-1')
        ->assertSee('Juli 2026')
        ->assertSee('Rp 350.000')
        ->assertSee('Transfer Bank')
        ->assertSee('Lunas')
        ->assertSee('data-toggle="tooltip" title="Detail"><i class="fas fa-eye"></i>', false)
        ->assertSee('data-toggle="tooltip" title="Ubah"><i class="fas fa-pencil-alt"></i>', false)
        ->assertSee('data-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i>', false)
        ->assertSee('<li class="active"><a class="nav-link" href="'.route('spp-payments.index').'">Pembayaran SPP</a></li>', false);
});

test('spp payments index page falls back to english when locale is en', function () {
    app()->setLocale('en');

    $this->get(route('spp-payments.index'))
        ->assertOk()
        ->assertSee('SPP Payment List')
        ->assertSee('Add New')
        ->assertSee('<li class="active"><a class="nav-link" href="'.route('spp-payments.index').'">SPP Payments</a></li>', false);
});

test('spp payments create page renders form with student and month options', function () {
    $student = Student::factory()->create([
        'nis' => '2026000123',
        'name' => 'Nadia Vega',
    ]);

    $this->get(route('spp-payments.create'))
        ->assertOk()
        ->assertViewIs('modules.spp-payments.create')
        ->assertSee('Tambah Pembayaran SPP')
        ->assertSee('2026000123 - Nadia Vega')
        ->assertSee('Januari')
        ->assertSee('name="invoice_number"', false)
        ->assertSee('name="student_id"', false)
        ->assertSee('name="amount"', false);
});

test('can store a new spp payment and flash swal success message in indonesian', function () {
    $student = Student::factory()->create();

    $payload = [
        'student_id' => $student->id,
        'invoice_number' => 'SPP-2026-00099',
        'month' => 8,
        'year' => 2026,
        'amount' => 500000,
        'payment_date' => '2026-08-10',
        'payment_method' => 'cash',
        'status' => 'paid',
        'notes' => 'Pembayaran bulan Agustus',
    ];

    $this->post(route('spp-payments.store'), $payload)
        ->assertRedirect(route('spp-payments.index'))
        ->assertSessionHas('success', 'Pembayaran SPP berhasil ditambahkan.');

    $this->assertDatabaseHas('spp_payments', [
        'student_id' => $student->id,
        'invoice_number' => 'SPP-2026-00099',
        'month' => 8,
        'year' => 2026,
        'amount' => 500000,
        'payment_method' => 'cash',
        'status' => 'paid',
    ]);

    $this->get(route('spp-payments.index'))
        ->assertOk()
        ->assertSee('swal("Berhasil", "Pembayaran SPP berhasil ditambahkan.", \'success\');', false);
});

test('storing an spp payment validates required fields, student relation, and unique invoice with swal error', function (array $overrides, string $errorField, string $expectedMessage) {
    $student = Student::factory()->create();
    SppPayment::factory()->for($student)->create([
        'invoice_number' => 'SPP-TAKEN-01',
    ]);

    $payload = array_merge([
        'student_id' => $student->id,
        'invoice_number' => 'SPP-2026-00100',
        'month' => 1,
        'year' => 2026,
        'amount' => 250000,
        'payment_date' => '2026-01-05',
        'payment_method' => 'cash',
        'status' => 'paid',
        'notes' => null,
    ], $overrides);

    $this->from(route('spp-payments.create'))
        ->post(route('spp-payments.store'), $payload)
        ->assertRedirect(route('spp-payments.create'))
        ->assertSessionHasErrors([
            $errorField => $expectedMessage,
        ]);

    $this->followRedirects($this->from(route('spp-payments.create'))->post(route('spp-payments.store'), $payload))
        ->assertOk()
        ->assertSee('swal("Gagal",', false)
        ->assertSee($expectedMessage);
})->with([
    'missing student' => [['student_id' => ''], 'student_id', 'Kolom Siswa wajib diisi.'],
    'nonexistent student' => [['student_id' => 99999], 'student_id', 'Siswa yang dipilih tidak valid.'],
    'missing invoice' => [['invoice_number' => ''], 'invoice_number', 'Kolom No. Tagihan wajib diisi.'],
    'duplicate invoice' => [['invoice_number' => 'SPP-TAKEN-01'], 'invoice_number', 'No. Tagihan sudah digunakan.'],
    'invalid month' => [['month' => 13], 'month', 'Kolom Bulan harus bernilai antara 1 dan 12.'],
    'invalid payment method' => [['payment_method' => 'crypto'], 'payment_method', 'Metode Pembayaran yang dipilih tidak valid.'],
    'invalid status' => [['status' => 'pending'], 'status', 'Status yang dipilih tidak valid.'],
]);

test('spp payments show page displays details and icon back button with tooltip', function () {
    $sppPayment = SppPayment::factory()->create([
        'invoice_number' => 'SPP-2026-00777',
    ]);

    $this->get(route('spp-payments.show', $sppPayment))
        ->assertOk()
        ->assertViewIs('modules.spp-payments.show')
        ->assertSee('Detail Pembayaran SPP')
        ->assertSee('SPP-2026-00777')
        ->assertSee('data-toggle="tooltip" title="Kembali"><i class="fas fa-arrow-left"></i>', false)
        ->assertSee('data-toggle="tooltip" title="Ubah"><i class="fas fa-pencil-alt"></i>', false);
});

test('spp payments edit page displays existing payment', function () {
    $sppPayment = SppPayment::factory()->create([
        'invoice_number' => 'SPP-2026-00888',
    ]);

    $this->get(route('spp-payments.edit', $sppPayment))
        ->assertOk()
        ->assertViewIs('modules.spp-payments.edit')
        ->assertSee('Ubah Pembayaran SPP')
        ->assertSee('value="SPP-2026-00888"', false);
});

test('can update an spp payment and allow keeping own invoice_number', function () {
    $sppPayment = SppPayment::factory()->create([
        'invoice_number' => 'SPP-2026-00555',
        'status' => 'unpaid',
    ]);

    $this->put(route('spp-payments.update', $sppPayment), [
        'student_id' => $sppPayment->student_id,
        'invoice_number' => 'SPP-2026-00555',
        'month' => $sppPayment->month,
        'year' => $sppPayment->year,
        'amount' => 400000,
        'payment_date' => '2026-09-01',
        'payment_method' => 'transfer',
        'status' => 'paid',
        'notes' => 'Lunas via transfer',
    ])
        ->assertRedirect(route('spp-payments.index'))
        ->assertSessionHas('success', 'Pembayaran SPP berhasil diperbarui.');

    $sppPayment->refresh();
    expect($sppPayment->status)->toBe('paid')
        ->and($sppPayment->amount)->toBe(400000);
});

test('updating an spp payment rejects duplicate invoice_number from another payment', function () {
    SppPayment::factory()->create([
        'invoice_number' => 'SPP-2026-EXISTING',
    ]);
    $sppPayment = SppPayment::factory()->create();

    $this->from(route('spp-payments.edit', $sppPayment))
        ->put(route('spp-payments.update', $sppPayment), [
            'student_id' => $sppPayment->student_id,
            'invoice_number' => 'SPP-2026-EXISTING',
            'month' => $sppPayment->month,
            'year' => $sppPayment->year,
            'amount' => $sppPayment->amount,
            'payment_date' => $sppPayment->payment_date->format('Y-m-d'),
            'payment_method' => $sppPayment->payment_method,
            'status' => $sppPayment->status,
        ])
        ->assertRedirect(route('spp-payments.edit', $sppPayment))
        ->assertSessionHasErrors([
            'invoice_number' => 'No. Tagihan sudah digunakan.',
        ]);
});

test('can delete an spp payment and deleting a student cascades to their spp payments', function () {
    $student = Student::factory()->create();
    $sppPayment = SppPayment::factory()->for($student)->create();

    $this->delete(route('spp-payments.destroy', $sppPayment))
        ->assertRedirect(route('spp-payments.index'))
        ->assertSessionHas('success', 'Pembayaran SPP berhasil dihapus.');

    $this->assertDatabaseMissing('spp_payments', [
        'id' => $sppPayment->id,
    ]);

    $cascadedPayment = SppPayment::factory()->for($student)->create();
    $student->delete();

    $this->assertDatabaseMissing('spp_payments', [
        'id' => $cascadedPayment->id,
    ]);
});
