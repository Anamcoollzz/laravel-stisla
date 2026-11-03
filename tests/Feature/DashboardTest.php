<?php

use App\Models\SchoolClass;
use App\Models\SppPayment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\SchoolClassSeeder;
use Database\Seeders\SppPaymentSeeder;
use Database\Seeders\StudentSeeder;
use Database\Seeders\SubjectSeeder;
use Database\Seeders\TeacherSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->actingAsAdmin());

test('dashboard page renders successfully at /dashboard', function () {
    $this->get('/dashboard')
        ->assertOk()
        ->assertViewIs('dashboard')
        ->assertSee('Dasbor')
        ->assertSee('Total Siswa')
        ->assertSee('Total Guru')
        ->assertSee('Total Kelas')
        ->assertSee('Total Mata Pelajaran')
        ->assertSee('Total SPP Terbayar')
        ->assertSee('Tren Penerimaan SPP Bulanan');
});

test('dashboard displays calculated statistics and recent records accurately', function () {
    $this->seed([
        UserSeeder::class,
        SchoolClassSeeder::class,
        TeacherSeeder::class,
        StudentSeeder::class,
        SubjectSeeder::class,
        SppPaymentSeeder::class,
    ]);

    $admin = User::query()->where('email', 'admin@example.com')->first();
    $studentCount = Student::query()->count();
    $teacherCount = Teacher::query()->count();
    $classCount = SchoolClass::query()->count();
    $subjectCount = Subject::query()->count();

    $recentPayment = SppPayment::query()->latest('payment_date')->latest('id')->first();

    $this->actingAs($admin)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee((string) $studentCount)
        ->assertSee((string) $teacherCount)
        ->assertSee((string) $classCount)
        ->assertSee((string) $subjectCount)
        ->assertSee($recentPayment->invoice_number);
});

test('dashboard handles empty data state gracefully', function () {
    $this->get('/dashboard')
        ->assertOk()
        ->assertSee('0')
        ->assertSee('Belum ada riwayat pembayaran.')
        ->assertSee('Belum ada data siswa.');
});
