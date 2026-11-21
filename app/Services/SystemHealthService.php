<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SystemHealthService
{
    /**
     * Run all system health diagnostics and return structured results.
     *
     * @return array<string, mixed>
     */
    public function runChecks(): array
    {
        $startedAt = microtime(true);

        $checks = [
            $this->checkDatabase(),
            $this->checkCache(),
            $this->checkDiskSpace(),
            $this->checkStorageDirectories(),
            $this->checkStorageSymlink(),
            $this->checkQueueHealth(),
            $this->checkAppSecurity(),
            $this->checkPhpExtensions(),
        ];

        $healthyCount = count(array_filter($checks, static fn (array $c): bool => $c['status'] === 'healthy'));
        $warningCount = count(array_filter($checks, static fn (array $c): bool => $c['status'] === 'warning'));
        $criticalCount = count(array_filter($checks, static fn (array $c): bool => $c['status'] === 'critical'));

        $overallStatus = match (true) {
            $criticalCount > 0 => 'critical',
            $warningCount > 0 => 'warning',
            default => 'healthy',
        };

        return [
            'overall_status' => $overallStatus,
            'counts' => [
                'total' => count($checks),
                'healthy' => $healthyCount,
                'warning' => $warningCount,
                'critical' => $criticalCount,
            ],
            'checks' => $checks,
            'environment' => $this->getEnvironmentInfo(),
            'total_duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            'checked_at' => now()->format('d M Y, H:i:s'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function checkDatabase(): array
    {
        $start = microtime(true);
        $connection = (string) config('database.default');

        try {
            DB::connection($connection)->getPdo();
            DB::connection($connection)->select('SELECT 1');
            $ms = (int) round((microtime(true) - $start) * 1000);

            return [
                'name' => __('Database Connection'),
                'category' => __('Core Infrastructure'),
                'status' => $ms > 500 ? 'warning' : 'healthy',
                'value' => strtoupper($connection)." ({$ms} ms)",
                'message' => $ms > 500
                    ? __('Database connected, but query latency is elevated.')
                    : __('Database connection is active and responsive.'),
                'duration_ms' => $ms,
            ];
        } catch (\Throwable $e) {
            return [
                'name' => __('Database Connection'),
                'category' => __('Core Infrastructure'),
                'status' => 'critical',
                'value' => strtoupper($connection),
                'message' => __('Database connection failed: :error', ['error' => $e->getMessage()]),
                'duration_ms' => (int) round((microtime(true) - $start) * 1000),
            ];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function checkCache(): array
    {
        $start = microtime(true);
        $driver = (string) config('cache.default');
        $probeKey = 'stisla.health_probe.'.uniqid('', true);

        try {
            Cache::put($probeKey, 'ok', 10);
            $val = Cache::get($probeKey);
            Cache::forget($probeKey);
            $ms = (int) round((microtime(true) - $start) * 1000);

            if ($val !== 'ok') {
                return [
                    'name' => __('Cache Store'),
                    'category' => __('Core Infrastructure'),
                    'status' => 'warning',
                    'value' => strtoupper($driver),
                    'message' => __('Cache write/read verification returned unexpected value.'),
                    'duration_ms' => $ms,
                ];
            }

            return [
                'name' => __('Cache Store'),
                'category' => __('Core Infrastructure'),
                'status' => 'healthy',
                'value' => strtoupper($driver)." ({$ms} ms)",
                'message' => __('Cache read/write operations succeeded.'),
                'duration_ms' => $ms,
            ];
        } catch (\Throwable $e) {
            return [
                'name' => __('Cache Store'),
                'category' => __('Core Infrastructure'),
                'status' => 'critical',
                'value' => strtoupper($driver),
                'message' => __('Cache probe failed: :error', ['error' => $e->getMessage()]),
                'duration_ms' => (int) round((microtime(true) - $start) * 1000),
            ];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function checkDiskSpace(): array
    {
        $start = microtime(true);
        $path = base_path();
        $free = @disk_free_space($path);
        $total = @disk_total_space($path);

        if ($free === false || $total === false || $total <= 0) {
            return [
                'name' => __('Disk Space'),
                'category' => __('Server Resources'),
                'status' => 'warning',
                'value' => __('Unknown'),
                'message' => __('Unable to determine disk space metrics.'),
                'duration_ms' => (int) round((microtime(true) - $start) * 1000),
            ];
        }

        $usedPercent = round((($total - $free) / $total) * 100, 1);
        $freeFormatted = $this->formatBytes((int) $free);
        $totalFormatted = $this->formatBytes((int) $total);

        $status = match (true) {
            $usedPercent >= 95 => 'critical',
            $usedPercent >= 85 => 'warning',
            default => 'healthy',
        };

        return [
            'name' => __('Disk Space'),
            'category' => __('Server Resources'),
            'status' => $status,
            'value' => "{$freeFormatted} / {$totalFormatted} ({$usedPercent}% used)",
            'message' => $status === 'healthy'
                ? __('Sufficient disk space available.')
                : __('Disk usage is high (:percent%). Consider freeing space.', ['percent' => $usedPercent]),
            'duration_ms' => (int) round((microtime(true) - $start) * 1000),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function checkStorageDirectories(): array
    {
        $start = microtime(true);
        $paths = [
            'storage/app' => storage_path('app'),
            'storage/app/public' => storage_path('app/public'),
            'storage/framework' => storage_path('framework'),
            'storage/logs' => storage_path('logs'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];

        $unwritable = [];
        foreach ($paths as $label => $fullPath) {
            if (! is_dir($fullPath) || ! is_writable($fullPath)) {
                $unwritable[] = $label;
            }
        }

        return [
            'name' => __('Storage & Cache Permissions'),
            'category' => __('Filesystem'),
            'status' => $unwritable === [] ? 'healthy' : 'critical',
            'value' => $unwritable === []
                ? __('All :count directories writable', ['count' => count($paths)])
                : __('Unwritable: :dirs', ['dirs' => implode(', ', $unwritable)]),
            'message' => $unwritable === []
                ? __('All required storage and bootstrap cache directories are writable.')
                : __('Some directories are missing or not writable.'),
            'duration_ms' => (int) round((microtime(true) - $start) * 1000),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function checkStorageSymlink(): array
    {
        $start = microtime(true);
        $publicStorage = public_path('storage');
        $exists = is_link($publicStorage) || is_dir($publicStorage);

        return [
            'name' => __('Public Storage Link'),
            'category' => __('Filesystem'),
            'status' => $exists ? 'healthy' : 'warning',
            'value' => $exists ? __('Linked') : __('Missing'),
            'message' => $exists
                ? __('public/storage symlink is present.')
                : __('Run "php artisan storage:link" so uploaded public files are accessible via URL.'),
            'duration_ms' => (int) round((microtime(true) - $start) * 1000),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function checkQueueHealth(): array
    {
        $start = microtime(true);
        $driver = (string) config('queue.default');
        $failed = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
        $pending = Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0;

        $status = $failed > 0 ? 'warning' : 'healthy';

        return [
            'name' => __('Queue & Failed Jobs'),
            'category' => __('Background Workers'),
            'status' => $status,
            'value' => strtoupper($driver)." ({$pending} pending, {$failed} failed)",
            'message' => $failed > 0
                ? __(':count failed job(s) detected. Inspect them in Queue Monitor.', ['count' => $failed])
                : __('No failed jobs in the queue.'),
            'duration_ms' => (int) round((microtime(true) - $start) * 1000),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function checkAppSecurity(): array
    {
        $start = microtime(true);
        $hasKey = ! empty(config('app.key'));
        $env = (string) config('app.env');
        $debug = (bool) config('app.debug');

        if (! $hasKey) {
            $status = 'critical';
            $message = __('APP_KEY is missing! Run "php artisan key:generate".');
        } elseif ($env === 'production' && $debug) {
            $status = 'warning';
            $message = __('APP_DEBUG is enabled in production. Disable it for security.');
        } else {
            $status = 'healthy';
            $message = __('Application key and debug mode are properly configured.');
        }

        return [
            'name' => __('Application Configuration'),
            'category' => __('Security'),
            'status' => $status,
            'value' => strtoupper($env).' / Debug: '.($debug ? 'ON' : 'OFF'),
            'message' => $message,
            'duration_ms' => (int) round((microtime(true) - $start) * 1000),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function checkPhpExtensions(): array
    {
        $start = microtime(true);
        $required = ['pdo', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'curl', 'fileinfo', 'zip'];
        $missing = array_values(array_filter($required, static fn (string $ext): bool => ! extension_loaded($ext)));

        return [
            'name' => __('PHP Extensions'),
            'category' => __('Runtime'),
            'status' => $missing === [] ? 'healthy' : 'critical',
            'value' => $missing === []
                ? __(':count/:count loaded', ['count' => count($required)])
                : __('Missing: :exts', ['exts' => implode(', ', $missing)]),
            'message' => $missing === []
                ? __('All required PHP extensions are installed.')
                : __('Install missing PHP extensions for full framework compatibility.'),
            'duration_ms' => (int) round((microtime(true) - $start) * 1000),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function getEnvironmentInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'os' => PHP_OS_FAMILY.' ('.php_uname('m').')',
            'server_software' => (string) ($_SERVER['SERVER_SOFTWARE'] ?? php_sapi_name()),
            'memory_limit' => (string) ini_get('memory_limit'),
            'memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'upload_max_filesize' => (string) ini_get('upload_max_filesize'),
            'post_max_size' => (string) ini_get('post_max_size'),
            'max_execution_time' => (string) ini_get('max_execution_time').'s',
            'timezone' => (string) config('app.timezone'),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        return round($bytes / (1024 ** $power), 2).' '.$units[$power];
    }
}
