<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaravelLogController extends Controller
{
    /**
     * Maximum bytes to read from a log file (5 MB).
     */
    private const MAX_READ_BYTES = 5 * 1024 * 1024;

    /**
     * Display the Laravel log viewer with DataTable.
     */
    public function index(Request $request): View
    {
        Gate::authorize('laravel-logs.view');

        $files = $this->getLogFiles();
        $requestedFile = basename((string) $request->query('file', ''));

        $currentFile = null;
        if ($requestedFile !== '' && collect($files)->contains('name', $requestedFile)) {
            $currentFile = $requestedFile;
        } elseif (collect($files)->contains('name', 'laravel.log')) {
            $currentFile = 'laravel.log';
        } elseif (! empty($files)) {
            $currentFile = $files[0]['name'];
        }

        $entries = [];
        $isTruncated = false;
        $currentFileMeta = null;

        if ($currentFile !== null) {
            $currentFileMeta = collect($files)->firstWhere('name', $currentFile);
            $filePath = $this->resolveSafeLogPath($currentFile);

            if ($filePath !== null) {
                [$entries, $isTruncated] = $this->parseLogFile($filePath);
            }
        }

        $stats = [
            'total' => count($entries),
            'error' => collect($entries)->whereIn('level', ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'])->count(),
            'warning' => collect($entries)->where('level', 'WARNING')->count(),
            'info' => collect($entries)->whereIn('level', ['INFO', 'DEBUG', 'NOTICE'])->count(),
        ];

        return view('modules.laravel-logs.index', compact(
            'files',
            'currentFile',
            'currentFileMeta',
            'entries',
            'stats',
            'isTruncated'
        ));
    }

    /**
     * Download the selected log file.
     */
    public function download(Request $request): BinaryFileResponse|RedirectResponse
    {
        Gate::authorize('laravel-logs.view');

        $file = basename((string) $request->query('file', ''));
        $path = $this->resolveSafeLogPath($file);

        if ($path === null) {
            return redirect()->route('laravel-logs.index')
                ->with('error', __('File log tidak ditemukan.'));
        }

        return response()->download($path, $file);
    }

    /**
     * Clear (empty) the contents of the selected log file.
     */
    public function clear(Request $request): RedirectResponse
    {
        Gate::authorize('laravel-logs.delete');

        $file = basename((string) $request->input('file', ''));
        $path = $this->resolveSafeLogPath($file);

        if ($path === null) {
            return redirect()->route('laravel-logs.index')
                ->with('error', __('File log tidak ditemukan.'));
        }

        File::put($path, '');

        return redirect()->route('laravel-logs.index', ['file' => $file])
            ->with('success', __('Isi file log :file berhasil dikosongkan.', ['file' => $file]));
    }

    /**
     * Delete the selected log file.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Gate::authorize('laravel-logs.delete');

        $file = basename((string) $request->input('file', ''));
        $path = $this->resolveSafeLogPath($file);

        if ($path === null) {
            return redirect()->route('laravel-logs.index')
                ->with('error', __('File log tidak ditemukan.'));
        }

        File::delete($path);

        return redirect()->route('laravel-logs.index')
            ->with('success', __('File log :file berhasil dihapus.', ['file' => $file]));
    }

    /**
     * List all .log files in storage/logs sorted by last modified descending.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getLogFiles(): array
    {
        $logDir = storage_path('logs');
        if (! File::isDirectory($logDir)) {
            return [];
        }

        $files = [];
        foreach (File::files($logDir) as $fileInfo) {
            if ($fileInfo->getExtension() !== 'log') {
                continue;
            }

            $sizeBytes = $fileInfo->getSize();
            $files[] = [
                'name' => $fileInfo->getFilename(),
                'size' => $this->formatBytes($sizeBytes),
                'size_bytes' => $sizeBytes,
                'modified_at' => Carbon::createFromTimestamp($fileInfo->getMTime())->format('d/m/Y H:i:s'),
                'timestamp' => $fileInfo->getMTime(),
            ];
        }

        usort($files, fn (array $a, array $b) => $b['timestamp'] <=> $a['timestamp']);

        return $files;
    }

    /**
     * Resolve a safe path inside storage/logs directory.
     */
    protected function resolveSafeLogPath(string $filename): ?string
    {
        if ($filename === '' || ! str_ends_with($filename, '.log')) {
            return null;
        }

        $baseDir = realpath(storage_path('logs'));
        $target = realpath(storage_path('logs'.DIRECTORY_SEPARATOR.basename($filename)));

        if ($baseDir === false || $target === false) {
            return null;
        }

        if (! str_starts_with($target, $baseDir.DIRECTORY_SEPARATOR)) {
            return null;
        }

        return File::isFile($target) ? $target : null;
    }

    /**
     * Parse a Laravel/Monolog file into structured entries (newest first).
     *
     * @return array{0: array<int, array<string, mixed>>, 1: bool}
     */
    protected function parseLogFile(string $filePath): array
    {
        $fileSize = File::size($filePath);
        $isTruncated = false;

        if ($fileSize === 0) {
            return [[], false];
        }

        if ($fileSize > self::MAX_READ_BYTES) {
            $isTruncated = true;
            $handle = fopen($filePath, 'rb');
            if ($handle === false) {
                return [[], false];
            }
            fseek($handle, -self::MAX_READ_BYTES, SEEK_END);
            $content = (string) fread($handle, self::MAX_READ_BYTES);
            fclose($handle);

            // Drop potentially cut first partial line
            $firstNewline = strpos($content, "\n");
            if ($firstNewline !== false) {
                $content = substr($content, $firstNewline + 1);
            }
        } else {
            $content = File::get($filePath);
        }

        $lines = preg_split("/\r\n|\n|\r/", $content) ?: [];
        $pattern = '/^\[(\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:[+-]\d{2}:?\d{2}|Z)?)\]\s+([^\s.]+)\.(\w+):\s*(.*)$/';

        $entries = [];
        $current = null;

        foreach ($lines as $line) {
            if (preg_match($pattern, $line, $matches) === 1) {
                if ($current !== null) {
                    $entries[] = $current;
                }

                $current = [
                    'timestamp' => $matches[1],
                    'env' => $matches[2],
                    'level' => strtoupper($matches[3]),
                    'message' => $matches[4],
                    'stack' => '',
                ];
            } elseif ($current !== null) {
                $current['stack'] .= ($current['stack'] === '' ? '' : "\n").$line;
            } elseif (trim($line) !== '') {
                // Fallback for non-standard log files (e.g., browser.log)
                $entries[] = [
                    'timestamp' => '-',
                    'env' => 'raw',
                    'level' => 'INFO',
                    'message' => $line,
                    'stack' => '',
                ];
            }
        }

        if ($current !== null) {
            $entries[] = $current;
        }

        $entries = array_reverse($entries);

        foreach ($entries as $idx => &$entry) {
            $entry['id'] = $idx + 1;
            $entry['stack'] = trim($entry['stack']);
        }

        return [$entries, $isTruncated];
    }

    /**
     * Format byte size into human-readable string.
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 2).' KB';
        }

        return round($bytes / 1048576, 2).' MB';
    }
}
