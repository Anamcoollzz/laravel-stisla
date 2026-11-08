<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ProductBannerController;
use App\Http\Controllers\ProductBrandController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductColorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSizeController;
use App\Http\Controllers\ProductVendorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SppPaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/pages/index.html');
Route::get('/lang/{locale}', [LocaleController::class, 'switch']);
Route::get('/privacy-policy', PrivacyPolicyController::class)->name('privacy-policy');

Route::prefix('app')->group(function () {
    Route::redirect('/', '/app/dashboard');
    Route::get('/privacy-policy', PrivacyPolicyController::class);

    Route::get('/lang/{locale}', [LocaleController::class, 'switch'])->name('lang.switch');

    Route::middleware('guest')->group(function () {
        Route::get('/auth/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/auth/login', [LoginController::class, 'login'])->name('login.attempt');

        Route::get('/auth/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/auth/register', [RegisterController::class, 'register'])->name('register.store');

        Route::get('/auth/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('/auth/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('/auth/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/auth/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
    });

    Route::post('/auth/logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::get('/settings/{group}', [SettingController::class, 'show'])->name('settings.show');
        Route::put('/settings/{group}', [SettingController::class, 'update'])->name('settings.update');
        Route::delete('/settings/{group}', [SettingController::class, 'reset'])->name('settings.reset');

        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

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

        Route::post('users/{user}/duplicate', [UserController::class, 'duplicate'])->name('users.duplicate');
        Route::get('users/export', [UserController::class, 'export'])->name('users.export');
        Route::get('users/export-pdf', [UserController::class, 'exportPdf'])->name('users.exportPdf');
        Route::get('users/template', [UserController::class, 'template'])->name('users.template');
        Route::post('users/import', [UserController::class, 'import'])->name('users.import');
        Route::resource('users', UserController::class);

        Route::post('fields/{field}/duplicate', [FieldController::class, 'duplicate'])->name('fields.duplicate');
        Route::get('fields/export-pdf', [FieldController::class, 'exportPdf'])->name('fields.exportPdf');
        Route::resource('fields', FieldController::class);

        Route::post('roles/{role}/duplicate', [RoleController::class, 'duplicate'])->name('roles.duplicate');
        Route::get('roles/export-pdf', [RoleController::class, 'exportPdf'])->name('roles.exportPdf');
        Route::resource('roles', RoleController::class);

        Route::post('permissions/{permission}/duplicate', [PermissionController::class, 'duplicate'])->name('permissions.duplicate');
        Route::get('permissions/export-pdf', [PermissionController::class, 'exportPdf'])->name('permissions.exportPdf');
        Route::resource('permissions', PermissionController::class)->except('show');

        Route::post('menus/{menu}/duplicate', [MenuController::class, 'duplicate'])->name('menus.duplicate');
        Route::get('menus/export-pdf', [MenuController::class, 'exportPdf'])->name('menus.exportPdf');
        Route::resource('menus', MenuController::class);

        Route::post('tickets/{ticket}/duplicate', [TicketController::class, 'duplicate'])->name('tickets.duplicate');
        Route::get('tickets/export-pdf', [TicketController::class, 'exportPdf'])->name('tickets.exportPdf');
        Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
        Route::resource('tickets', TicketController::class);

        Route::post('product-categories/{productCategory}/duplicate', [ProductCategoryController::class, 'duplicate'])->name('product-categories.duplicate');
        Route::get('product-categories/export', [ProductCategoryController::class, 'export'])->name('product-categories.export');
        Route::get('product-categories/export-pdf', [ProductCategoryController::class, 'exportPdf'])->name('product-categories.exportPdf');
        Route::get('product-categories/template', [ProductCategoryController::class, 'template'])->name('product-categories.template');
        Route::post('product-categories/import', [ProductCategoryController::class, 'import'])->name('product-categories.import');
        Route::resource('product-categories', ProductCategoryController::class)->parameters([
            'product-categories' => 'productCategory',
        ]);

        Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
        Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
        Route::get('products/export-pdf', [ProductController::class, 'exportPdf'])->name('products.exportPdf');
        Route::get('products/template', [ProductController::class, 'template'])->name('products.template');
        Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
        Route::resource('products', ProductController::class);

        Route::post('product-brands/{productBrand}/duplicate', [ProductBrandController::class, 'duplicate'])->name('product-brands.duplicate');
        Route::get('product-brands/export', [ProductBrandController::class, 'export'])->name('product-brands.export');
        Route::get('product-brands/export-pdf', [ProductBrandController::class, 'exportPdf'])->name('product-brands.exportPdf');
        Route::get('product-brands/template', [ProductBrandController::class, 'template'])->name('product-brands.template');
        Route::post('product-brands/import', [ProductBrandController::class, 'import'])->name('product-brands.import');
        Route::resource('product-brands', ProductBrandController::class)->parameters([
            'product-brands' => 'productBrand',
        ]);

        Route::post('product-vendors/{productVendor}/duplicate', [ProductVendorController::class, 'duplicate'])->name('product-vendors.duplicate');
        Route::get('product-vendors/export', [ProductVendorController::class, 'export'])->name('product-vendors.export');
        Route::get('product-vendors/export-pdf', [ProductVendorController::class, 'exportPdf'])->name('product-vendors.exportPdf');
        Route::get('product-vendors/template', [ProductVendorController::class, 'template'])->name('product-vendors.template');
        Route::post('product-vendors/import', [ProductVendorController::class, 'import'])->name('product-vendors.import');
        Route::resource('product-vendors', ProductVendorController::class)->parameters([
            'product-vendors' => 'productVendor',
        ]);

        Route::post('product-sizes/{productSize}/duplicate', [ProductSizeController::class, 'duplicate'])->name('product-sizes.duplicate');
        Route::get('product-sizes/export', [ProductSizeController::class, 'export'])->name('product-sizes.export');
        Route::get('product-sizes/export-pdf', [ProductSizeController::class, 'exportPdf'])->name('product-sizes.exportPdf');
        Route::get('product-sizes/template', [ProductSizeController::class, 'template'])->name('product-sizes.template');
        Route::post('product-sizes/import', [ProductSizeController::class, 'import'])->name('product-sizes.import');
        Route::resource('product-sizes', ProductSizeController::class)->parameters([
            'product-sizes' => 'productSize',
        ]);

        Route::post('product-colors/{productColor}/duplicate', [ProductColorController::class, 'duplicate'])->name('product-colors.duplicate');
        Route::get('product-colors/export', [ProductColorController::class, 'export'])->name('product-colors.export');
        Route::get('product-colors/export-pdf', [ProductColorController::class, 'exportPdf'])->name('product-colors.exportPdf');
        Route::get('product-colors/template', [ProductColorController::class, 'template'])->name('product-colors.template');
        Route::post('product-colors/import', [ProductColorController::class, 'import'])->name('product-colors.import');
        Route::resource('product-colors', ProductColorController::class)->parameters([
            'product-colors' => 'productColor',
        ]);

        Route::post('product-banners/{productBanner}/duplicate', [ProductBannerController::class, 'duplicate'])->name('product-banners.duplicate');
        Route::get('product-banners/export', [ProductBannerController::class, 'export'])->name('product-banners.export');
        Route::get('product-banners/export-pdf', [ProductBannerController::class, 'exportPdf'])->name('product-banners.exportPdf');
        Route::get('product-banners/template', [ProductBannerController::class, 'template'])->name('product-banners.template');
        Route::post('product-banners/import', [ProductBannerController::class, 'import'])->name('product-banners.import');
        Route::resource('product-banners', ProductBannerController::class)->parameters([
            'product-banners' => 'productBanner',
        ]);

        Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
        Route::post('chat/read-all', [ChatController::class, 'markAllAsRead'])->name('chat.readAll');
        Route::post('chat/{user}', [ChatController::class, 'send'])->name('chat.send');
        Route::get('chat/{user}/messages', [ChatController::class, 'fetchMessages'])->name('chat.messages');
        Route::delete('chat/{user}/clear', [ChatController::class, 'clear'])->name('chat.clear');

        Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('backups', [BackupController::class, 'create'])->name('backups.create');
        Route::get('backups/{filename}/download', [BackupController::class, 'download'])->name('backups.download');
        Route::delete('backups/{filename}', [BackupController::class, 'destroy'])->name('backups.destroy');
    });
});

require __DIR__.'/pages-web.php';
require __DIR__.'/ecommerce-pages-web.php';
