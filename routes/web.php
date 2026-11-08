<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SppPaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/pages/index.html');

Route::get('/lang/{locale}', [LocaleController::class, 'switch'])->name('lang.switch');

Route::middleware('guest')->group(function () {
    Route::get('/auth/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/auth/login', [LoginController::class, 'login'])->name('login.attempt');

    Route::get('/auth/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/auth/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/auth/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/auth/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
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

    Route::resource('fields', FieldController::class);

    Route::resource('roles', RoleController::class);

    Route::resource('permissions', PermissionController::class)->except('show');

    Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    Route::resource('tickets', TicketController::class);
});

require __DIR__.'/pages-web.php';
