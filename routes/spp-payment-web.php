<?php

use App\Http\Controllers\FieldController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SppPaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::prefix('app')->middleware('auth')->group(function () {
    Route::post('classes/{schoolClass}/duplicate', [SchoolClassController::class, 'duplicate'])->name('classes.duplicate');
    Route::get('classes/export', [SchoolClassController::class, 'export'])->name('classes.export');
    Route::get('classes/export-pdf', [SchoolClassController::class, 'exportPdf'])->name('classes.exportPdf');
    Route::get('classes/template', [SchoolClassController::class, 'template'])->name('classes.template');
    Route::post('classes/import', [SchoolClassController::class, 'import'])->name('classes.import');
    Route::resource('classes', SchoolClassController::class)->parameters([
        'classes' => 'schoolClass',
    ]);

    Route::post('teachers/{teacher}/duplicate', [TeacherController::class, 'duplicate'])->name('teachers.duplicate');
    Route::get('teachers/export', [TeacherController::class, 'export'])->name('teachers.export');
    Route::get('teachers/export-pdf', [TeacherController::class, 'exportPdf'])->name('teachers.exportPdf');
    Route::get('teachers/template', [TeacherController::class, 'template'])->name('teachers.template');
    Route::post('teachers/import', [TeacherController::class, 'import'])->name('teachers.import');
    Route::resource('teachers', TeacherController::class);

    Route::post('students/{student}/duplicate', [StudentController::class, 'duplicate'])->name('students.duplicate');
    Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
    Route::get('students/export-pdf', [StudentController::class, 'exportPdf'])->name('students.exportPdf');
    Route::get('students/template', [StudentController::class, 'template'])->name('students.template');
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
    Route::resource('students', StudentController::class);

    Route::post('subjects/{subject}/duplicate', [SubjectController::class, 'duplicate'])->name('subjects.duplicate');
    Route::get('subjects/export', [SubjectController::class, 'export'])->name('subjects.export');
    Route::get('subjects/export-pdf', [SubjectController::class, 'exportPdf'])->name('subjects.exportPdf');
    Route::get('subjects/template', [SubjectController::class, 'template'])->name('subjects.template');
    Route::post('subjects/import', [SubjectController::class, 'import'])->name('subjects.import');
    Route::resource('subjects', SubjectController::class);

    Route::post('spp-payments/{sppPayment}/duplicate', [SppPaymentController::class, 'duplicate'])->name('spp-payments.duplicate');
    Route::get('spp-payments/export', [SppPaymentController::class, 'export'])->name('spp-payments.export');
    Route::get('spp-payments/export-pdf', [SppPaymentController::class, 'exportPdf'])->name('spp-payments.exportPdf');
    Route::get('spp-payments/template', [SppPaymentController::class, 'template'])->name('spp-payments.template');
    Route::post('spp-payments/import', [SppPaymentController::class, 'import'])->name('spp-payments.import');
    Route::resource('spp-payments', SppPaymentController::class);

    Route::post('fields/{field}/duplicate', [FieldController::class, 'duplicate'])->name('fields.duplicate');
    Route::get('fields/export-pdf', [FieldController::class, 'exportPdf'])->name('fields.exportPdf');
    Route::resource('fields', FieldController::class);
});
