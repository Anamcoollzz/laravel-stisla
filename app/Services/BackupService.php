<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupService
{
    /**
     * Get the absolute path to the backup directory.
     */
    public function getBackupPath(): string
    {
        $path = storage_path('app/backups');
        File::ensureDirectoryExists($path);

        return $path;
    }

    /**
     * Get list of all existing backup files.
     *
     * @return Collection<int, array{name: string, path: string, size: int, size_formatted: string, created_at: Carbon}>
     */
    public function getBackups(): Collection
    {
        $dir = $this->getBackupPath();
        $files = File::files($dir);

        return collect($files)
            ->filter(fn ($file) => in_array($file->getExtension(), ['sql', 'sqlite', 'zip']))
            ->map(function ($file) {
                $mtime = Carbon::createFromTimestamp($file->getMTime());

                return [
                    'name' => $file->getFilename(),
                    'path' => $file->getRealPath(),
                    'size' => $file->getSize(),
                    'size_formatted' => $this->formatBytes($file->getSize()),
                    'formatted_size' => $this->formatBytes($file->getSize()),
                    'created_at' => $mtime,
                    'modified_at' => $mtime,
                ];
            })
            ->sortByDesc('created_at')
            ->values();
    }

    /**
     * Create a new database backup SQL dump.
     */
    public function createBackup(): string
    {
        $dir = $this->getBackupPath();
        $appName = preg_replace('/[^a-zA-Z0-9_-]/', '_', config('app.name', 'laravel'));
        $filename = strtolower($appName).'-backup-'.date('Y-m-d_His').'.sql';
        $filePath = $dir.DIRECTORY_SEPARATOR.$filename;

        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        $handle = fopen($filePath, 'w');

        fwrite($handle, "-- --------------------------------------------------------\n");
        fwrite($handle, '-- Database Backup: '.config('app.name')."\n");
        fwrite($handle, '-- Created At: '.now()->format('Y-m-d H:i:s')."\n");
        fwrite($handle, "-- Driver: {$driver}\n");
        fwrite($handle, "-- --------------------------------------------------------\n\n");

        if ($driver === 'sqlite') {
            $this->dumpSqlite($handle);
        } elseif ($driver === 'mysql') {
            $this->dumpMysql($handle);
        } else {
            $this->dumpGeneric($handle);
        }

        fclose($handle);

        return $filename;
    }

    /**
     * Dump SQLite database tables and records.
     *
     * @param  resource  $handle
     */
    protected function dumpSqlite($handle): void
    {
        fwrite($handle, "PRAGMA foreign_keys = OFF;\n\n");

        $tables = DB::select("SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        $pdo = DB::connection()->getPdo();

        foreach ($tables as $table) {
            $tableName = $table->name;
            fwrite($handle, "-- --------------------------------------------------------\n");
            fwrite($handle, "-- Table structure for `{$tableName}`\n");
            fwrite($handle, "-- --------------------------------------------------------\n");
            fwrite($handle, "DROP TABLE IF EXISTS \"{$tableName}\";\n");
            fwrite($handle, $table->sql.";\n\n");

            $rows = DB::table($tableName)->get();
            if ($rows->isNotEmpty()) {
                fwrite($handle, "-- Dumping data for `{$tableName}`\n");
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $cols = implode(', ', array_map(fn ($c) => "\"{$c}\"", array_keys($rowArray)));
                    $vals = implode(', ', array_map(function ($v) use ($pdo) {
                        if (is_null($v)) {
                            return 'NULL';
                        }

                        return $pdo->quote((string) $v);
                    }, array_values($rowArray)));

                    fwrite($handle, "INSERT INTO \"{$tableName}\" ({$cols}) VALUES ({$vals});\n");
                }
                fwrite($handle, "\n");
            }
        }

        fwrite($handle, "PRAGMA foreign_keys = ON;\n");
    }

    /**
     * Dump MySQL database tables and records.
     *
     * @param  resource  $handle
     */
    protected function dumpMysql($handle): void
    {
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $keyName = 'Tables_in_'.$dbName;
        $pdo = DB::connection()->getPdo();

        foreach ($tables as $table) {
            $tableName = $table->$keyName ?? array_values((array) $table)[0];
            $createTableResult = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createTable = $createTableResult[0]->{'Create Table'} ?? null;

            if ($createTable) {
                fwrite($handle, "-- --------------------------------------------------------\n");
                fwrite($handle, "-- Table structure for `{$tableName}`\n");
                fwrite($handle, "-- --------------------------------------------------------\n");
                fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
                fwrite($handle, $createTable.";\n\n");

                $rows = DB::table($tableName)->get();
                if ($rows->isNotEmpty()) {
                    fwrite($handle, "-- Dumping data for `{$tableName}`\n");
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $cols = implode(', ', array_map(fn ($c) => "`{$c}`", array_keys($rowArray)));
                        $vals = implode(', ', array_map(function ($v) use ($pdo) {
                            if (is_null($v)) {
                                return 'NULL';
                            }

                            return $pdo->quote((string) $v);
                        }, array_values($rowArray)));

                        fwrite($handle, "INSERT INTO `{$tableName}` ({$cols}) VALUES ({$vals});\n");
                    }
                    fwrite($handle, "\n");
                }
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
    }

    /**
     * Generic dump fallback using schema information.
     *
     * @param  resource  $handle
     */
    protected function dumpGeneric($handle): void
    {
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        $pdo = DB::connection()->getPdo();

        foreach ($tables as $tableName) {
            $rows = DB::table($tableName)->get();
            if ($rows->isNotEmpty()) {
                fwrite($handle, "-- Dumping data for `{$tableName}`\n");
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $cols = implode(', ', array_keys($rowArray));
                    $vals = implode(', ', array_map(function ($v) use ($pdo) {
                        if (is_null($v)) {
                            return 'NULL';
                        }

                        return $pdo->quote((string) $v);
                    }, array_values($rowArray)));

                    fwrite($handle, "INSERT INTO {$tableName} ({$cols}) VALUES ({$vals});\n");
                }
                fwrite($handle, "\n");
            }
        }
    }

    /**
     * Download backup file.
     */
    public function downloadBackup(string $filename): BinaryFileResponse
    {
        $filePath = $this->getBackupPath().DIRECTORY_SEPARATOR.basename($filename);

        abort_unless(File::exists($filePath), 404, __('Backup file not found.'));

        return response()->download($filePath);
    }

    /**
     * Delete backup file.
     */
    public function deleteBackup(string $filename): bool
    {
        $filePath = $this->getBackupPath().DIRECTORY_SEPARATOR.basename($filename);

        if (File::exists($filePath)) {
            return File::delete($filePath);
        }

        return false;
    }

    /**
     * Format bytes to readable human unit.
     */
    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
