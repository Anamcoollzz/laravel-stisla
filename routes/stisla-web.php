<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AppNotificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatabaseTableController;
use App\Http\Controllers\LaravelLogController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\LogRequestController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OurTeamController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\PreventInDemoMode;
use Illuminate\Support\Facades\Route;

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
        Route::get('/auth/activate/{id}/{hash}', [RegisterController::class, 'activate'])
            ->middleware('signed')
            ->name('register.activate');

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

        Route::post('users/{user}/duplicate', [UserController::class, 'duplicate'])->name('users.duplicate');
        Route::get('users/export', [UserController::class, 'export'])->name('users.export');
        Route::get('users/export-pdf', [UserController::class, 'exportPdf'])->name('users.exportPdf');
        Route::get('users/template', [UserController::class, 'template'])->name('users.template');
        Route::post('users/import', [UserController::class, 'import'])->name('users.import');
        Route::resource('users', UserController::class);

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

        Route::post('announcements/{announcement}/duplicate', [AnnouncementController::class, 'duplicate'])->name('announcements.duplicate');
        Route::patch('announcements/{announcement}/toggle-active', [AnnouncementController::class, 'toggleActive'])->name('announcements.toggleActive');
        Route::resource('announcements', AnnouncementController::class);

        Route::post('notifications/mark-all-read', [AppNotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
        Route::get('notifications/{notification}/read', [AppNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::resource('notifications', AppNotificationController::class);

        Route::post('our-teams/{ourTeam}/duplicate', [OurTeamController::class, 'duplicate'])->name('our-teams.duplicate');
        Route::resource('our-teams', OurTeamController::class)->parameters([
            'our-teams' => 'ourTeam',
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

        Route::middleware(PreventInDemoMode::class)->group(function () {
            Route::delete('log-requests/clear', [LogRequestController::class, 'clear'])->name('log-requests.clear');
            Route::resource('log-requests', LogRequestController::class)->only(['index', 'show', 'destroy'])->parameters([
                'log-requests' => 'logRequest',
            ]);

            Route::get('laravel-logs', [LaravelLogController::class, 'index'])->name('laravel-logs.index');
            Route::get('laravel-logs/download', [LaravelLogController::class, 'download'])->name('laravel-logs.download');
            Route::post('laravel-logs/clear', [LaravelLogController::class, 'clear'])->name('laravel-logs.clear');
            Route::delete('laravel-logs/delete', [LaravelLogController::class, 'destroy'])->name('laravel-logs.destroy');

            Route::get('database-tables', [DatabaseTableController::class, 'index'])->name('database-tables.index');
            Route::get('database-tables/{table}', [DatabaseTableController::class, 'show'])->name('database-tables.show');
            Route::put('database-tables/{table}/columns/{column}', [DatabaseTableController::class, 'updateColumn'])->name('database-tables.columns.update');
            Route::delete('database-tables/{table}/columns/{column}', [DatabaseTableController::class, 'destroyColumn'])->name('database-tables.columns.destroy');
            Route::put('database-tables/{table}/rows/{rowId}', [DatabaseTableController::class, 'updateRow'])->name('database-tables.rows.update');
            Route::delete('database-tables/{table}/rows/{rowId}', [DatabaseTableController::class, 'destroyRow'])->name('database-tables.rows.destroy');
        });
    });
});
