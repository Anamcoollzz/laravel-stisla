<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SppPaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/pages/index.html');

Route::middleware('guest')->group(function () {
    Route::get('/auth/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/auth/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/auth/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('classes/export', [SchoolClassController::class, 'export'])->name('classes.export');
    Route::get('classes/template', [SchoolClassController::class, 'template'])->name('classes.template');
    Route::post('classes/import', [SchoolClassController::class, 'import'])->name('classes.import');
    Route::resource('classes', SchoolClassController::class)->parameters([
        'classes' => 'schoolClass',
    ]);

    Route::get('teachers/export', [TeacherController::class, 'export'])->name('teachers.export');
    Route::get('teachers/template', [TeacherController::class, 'template'])->name('teachers.template');
    Route::post('teachers/import', [TeacherController::class, 'import'])->name('teachers.import');
    Route::resource('teachers', TeacherController::class);

    Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
    Route::get('students/template', [StudentController::class, 'template'])->name('students.template');
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
    Route::resource('students', StudentController::class);

    Route::get('subjects/export', [SubjectController::class, 'export'])->name('subjects.export');
    Route::get('subjects/template', [SubjectController::class, 'template'])->name('subjects.template');
    Route::post('subjects/import', [SubjectController::class, 'import'])->name('subjects.import');
    Route::resource('subjects', SubjectController::class);

    Route::get('spp-payments/export', [SppPaymentController::class, 'export'])->name('spp-payments.export');
    Route::get('spp-payments/template', [SppPaymentController::class, 'template'])->name('spp-payments.template');
    Route::post('spp-payments/import', [SppPaymentController::class, 'import'])->name('spp-payments.import');
    Route::resource('spp-payments', SppPaymentController::class);

    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::get('users/template', [UserController::class, 'template'])->name('users.template');
    Route::post('users/import', [UserController::class, 'import'])->name('users.import');
    Route::resource('users', UserController::class);

    Route::resource('roles', RoleController::class);
});

require __DIR__.'/pages-web.php';
