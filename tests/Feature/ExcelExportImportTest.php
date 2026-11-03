<?php

use App\Exports\SchoolClassesExport;
use App\Exports\SchoolClassesTemplateExport;
use App\Exports\SppPaymentsExport;
use App\Exports\SppPaymentsTemplateExport;
use App\Exports\StudentsExport;
use App\Exports\StudentsTemplateExport;
use App\Exports\SubjectsExport;
use App\Exports\SubjectsTemplateExport;
use App\Exports\TeachersExport;
use App\Exports\TeachersTemplateExport;
use App\Exports\UsersExport;
use App\Exports\UsersTemplateExport;
use App\Models\SchoolClass;
use App\Models\SppPayment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->actingAsAdmin());

test('classes export and template routes download expected excel files', function () {
    Excel::fake();

    $this->get(route('classes.export'))->assertOk();
    Excel::assertDownloaded('classes_'.date('Y-m-d_His').'.xlsx', function (SchoolClassesExport $export) {
        return true;
    });

    $this->get(route('classes.template'))->assertOk();
    Excel::assertDownloaded('classes_import_example.xlsx', function (SchoolClassesTemplateExport $export) {
        return true;
    });
});

test('can import classes from sample excel file', function () {
    $filePath = public_path('templates/classes_import_example.xlsx');
    expect(file_exists($filePath))->toBeTrue();

    $file = new UploadedFile(
        $filePath,
        'classes_import_example.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $this->post(route('classes.import'), ['file' => $file])
        ->assertRedirect(route('classes.index'))
        ->assertSessionHas('success');

    expect(SchoolClass::where('class_name', 'Kelas 10-MIPA-1')->exists())->toBeTrue();
    expect(SchoolClass::where('class_name', 'Kelas 10-IPS-1')->exists())->toBeTrue();
});

test('teachers export and template routes download expected excel files', function () {
    Excel::fake();

    $this->get(route('teachers.export'))->assertOk();
    Excel::assertDownloaded('teachers_'.date('Y-m-d_His').'.xlsx', function (TeachersExport $export) {
        return true;
    });

    $this->get(route('teachers.template'))->assertOk();
    Excel::assertDownloaded('teachers_import_example.xlsx', function (TeachersTemplateExport $export) {
        return true;
    });
});

test('can import teachers from sample excel file', function () {
    $filePath = public_path('templates/teachers_import_example.xlsx');
    expect(file_exists($filePath))->toBeTrue();

    $file = new UploadedFile(
        $filePath,
        'teachers_import_example.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $this->post(route('teachers.import'), ['file' => $file])
        ->assertRedirect(route('teachers.index'))
        ->assertSessionHas('success');

    expect(Teacher::where('nip', '198501012010011001')->exists())->toBeTrue();
    expect(Teacher::where('email', 'budi.santoso@sekolah.sch.id')->exists())->toBeTrue();
});

test('students export and template routes download expected excel files', function () {
    Excel::fake();

    $this->get(route('students.export'))->assertOk();
    Excel::assertDownloaded('students_'.date('Y-m-d_His').'.xlsx', function (StudentsExport $export) {
        return true;
    });

    $this->get(route('students.template'))->assertOk();
    Excel::assertDownloaded('students_import_example.xlsx', function (StudentsTemplateExport $export) {
        return true;
    });
});

test('can import students from sample excel file', function () {
    $filePath = public_path('templates/students_import_example.xlsx');
    expect(file_exists($filePath))->toBeTrue();

    $file = new UploadedFile(
        $filePath,
        'students_import_example.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $this->post(route('students.import'), ['file' => $file])
        ->assertRedirect(route('students.index'))
        ->assertSessionHas('success');

    expect(Student::where('nis', '2026000001')->exists())->toBeTrue();
    expect(Student::where('name', 'Ahmad Fauzi')->exists())->toBeTrue();
});

test('subjects export and template routes download expected excel files', function () {
    Excel::fake();

    $this->get(route('subjects.export'))->assertOk();
    Excel::assertDownloaded('subjects_'.date('Y-m-d_His').'.xlsx', function (SubjectsExport $export) {
        return true;
    });

    $this->get(route('subjects.template'))->assertOk();
    Excel::assertDownloaded('subjects_import_example.xlsx', function (SubjectsTemplateExport $export) {
        return true;
    });
});

test('can import subjects from sample excel file', function () {
    $filePath = public_path('templates/subjects_import_example.xlsx');
    expect(file_exists($filePath))->toBeTrue();

    $file = new UploadedFile(
        $filePath,
        'subjects_import_example.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $this->post(route('subjects.import'), ['file' => $file])
        ->assertRedirect(route('subjects.index'))
        ->assertSessionHas('success');

    expect(Subject::where('code', 'MTK-10')->exists())->toBeTrue();
    expect(Subject::where('code', 'BIN-10')->exists())->toBeTrue();
});

test('spp payments export and template routes download expected excel files', function () {
    Excel::fake();

    $this->get(route('spp-payments.export'))->assertOk();
    Excel::assertDownloaded('spp_payments_'.date('Y-m-d_His').'.xlsx', function (SppPaymentsExport $export) {
        return true;
    });

    $this->get(route('spp-payments.template'))->assertOk();
    Excel::assertDownloaded('spp_payments_import_example.xlsx', function (SppPaymentsTemplateExport $export) {
        return true;
    });
});

test('can import spp_payments from sample excel file', function () {
    // Student 2026000001 must exist first for SPP payment linking
    Student::factory()->create([
        'nis' => '2026000001',
        'name' => 'Ahmad Fauzi',
    ]);

    $filePath = public_path('templates/spp_payments_import_example.xlsx');
    expect(file_exists($filePath))->toBeTrue();

    $file = new UploadedFile(
        $filePath,
        'spp_payments_import_example.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $this->post(route('spp-payments.import'), ['file' => $file])
        ->assertRedirect(route('spp-payments.index'))
        ->assertSessionHas('success');

    expect(SppPayment::where('amount', 250000)->where('year', 2026)->exists())->toBeTrue();
});

test('users export and template routes download expected excel files', function () {
    Excel::fake();

    $this->get(route('users.export'))->assertOk();
    Excel::assertDownloaded('users_'.date('Y-m-d_His').'.xlsx', function (UsersExport $export) {
        return true;
    });

    $this->get(route('users.template'))->assertOk();
    Excel::assertDownloaded('users_import_example.xlsx', function (UsersTemplateExport $export) {
        return true;
    });
});

test('can import users from sample excel file', function () {
    $filePath = public_path('templates/users_import_example.xlsx');
    expect(file_exists($filePath))->toBeTrue();

    $file = new UploadedFile(
        $filePath,
        'users_import_example.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $this->post(route('users.import'), ['file' => $file])
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success');

    $importedUser = User::where('email', 'operator1@sekolah.sch.id')->first();
    expect($importedUser)->not->toBeNull();
    expect($importedUser->hasRole('staff'))->toBeTrue();
});

test('unauthorized user cannot import files without permission', function () {
    $this->actingAsUser(); // User role only has view permissions

    $filePath = public_path('templates/classes_import_example.xlsx');
    $file = new UploadedFile(
        $filePath,
        'classes_import_example.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $this->post(route('classes.import'), ['file' => $file])
        ->assertForbidden();
});
