<?php

namespace App\Http\Controllers;

use App\Services\BackupService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function __construct(
        protected BackupService $backupService
    ) {}

    /**
     * Display a listing of database backups.
     */
    public function index(): View
    {
        Gate::authorize('backup.view');

        $backups = $this->backupService->getBackups();
        $totalBytes = $backups->sum('size');
        $driver = config('database.default');

        return view('modules.backups.index', [
            'backups' => $backups,
            'totalCount' => $backups->count(),
            'totalSize' => $this->backupService->formatBytes($totalBytes),
            'latestBackup' => $backups->first(),
            'driver' => strtoupper($driver),
        ]);
    }

    /**
     * Create a new database backup.
     */
    public function create(): RedirectResponse
    {
        Gate::authorize('backup.create');

        try {
            $filename = $this->backupService->createBackup();

            return redirect()
                ->route('backups.index')
                ->with('success', __('Database backup created successfully: :filename', ['filename' => $filename]));
        } catch (\Throwable $e) {
            return redirect()
                ->route('backups.index')
                ->with('error', __('Backup failed: ').$e->getMessage());
        }
    }

    /**
     * Download the specified backup file.
     */
    public function download(string $filename): BinaryFileResponse
    {
        Gate::authorize('backup.download');

        return $this->backupService->downloadBackup($filename);
    }

    /**
     * Delete the specified backup file.
     */
    public function destroy(string $filename): RedirectResponse
    {
        Gate::authorize('backup.delete');

        $deleted = $this->backupService->deleteBackup($filename);

        if ($deleted) {
            return redirect()
                ->route('backups.index')
                ->with('success', __('Backup file deleted successfully.'));
        }

        return redirect()
            ->route('backups.index')
            ->with('error', __('Backup file could not be found or deleted.'));
    }
}
