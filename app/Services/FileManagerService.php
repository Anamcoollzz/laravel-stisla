<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileManagerService
{
    /**
     * File extensions supported by the built-in text editor.
     *
     * @var list<string>
     */
    private const EDITABLE_EXTENSIONS = [
        'txt', 'md', 'json', 'csv', 'log', 'xml', 'html', 'htm',
        'css', 'js', 'ts', 'yml', 'yaml', 'ini', 'conf', 'sql', 'svg',
    ];

    /**
     * Image extensions supported for inline modal preview.
     *
     * @var list<string>
     */
    private const IMAGE_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico',
    ];

    /**
     * Maximum file size (1 MB) allowed for the text editor.
     */
    private const MAX_EDITABLE_BYTES = 1048576;

    /**
     * Resolve visibility key ('public'|'private') to Laravel filesystem disk metadata.
     *
     * @return array{visibility: string, disk: string, label: string, root_label: string}
     */
    public function resolveDisk(?string $visibility): array
    {
        return match ($visibility) {
            'private' => [
                'visibility' => 'private',
                'disk' => 'local',
                'label' => __('Private Storage'),
                'root_label' => 'storage/app/private',
            ],
            default => [
                'visibility' => 'public',
                'disk' => 'public',
                'label' => __('Public Storage'),
                'root_label' => 'storage/app/public',
            ],
        };
    }

    /**
     * Normalize and validate a relative storage path against traversal attacks.
     */
    public function normalizePath(?string $path): string
    {
        $path = str_replace('\\', '/', trim((string) $path));

        if (str_contains($path, "\0")) {
            throw new InvalidArgumentException(__('Invalid path character detected.'));
        }

        $segments = array_values(array_filter(explode('/', $path), static fn (string $segment): bool => $segment !== ''));

        foreach ($segments as $segment) {
            if ($segment === '.' || $segment === '..') {
                throw new InvalidArgumentException(__('Path traversal is not allowed.'));
            }
        }

        return implode('/', $segments);
    }

    /**
     * Validate a single file or folder name (no slashes or traversal segments).
     */
    public function sanitizeName(string $name): string
    {
        $name = trim(str_replace(['\\', '/'], '', $name));

        if ($name === '' || $name === '.' || $name === '..' || str_contains($name, "\0")) {
            throw new InvalidArgumentException(__('Invalid file or folder name.'));
        }

        return $name;
    }

    /**
     * List folders and files inside the requested storage directory.
     *
     * @return array{
     *     visibility: string,
     *     disk: string,
     *     disk_label: string,
     *     root_label: string,
     *     path: string,
     *     parent_path: ?string,
     *     breadcrumbs: list<array{name: string, path: string}>,
     *     directories: list<array<string, mixed>>,
     *     files: list<array<string, mixed>>,
     *     stats: array{total_folders: int, total_files: int, total_size: int, total_size_formatted: string}
     * }
     */
    public function listDirectory(string $visibility, string $path = '', string $search = ''): array
    {
        $diskInfo = $this->resolveDisk($visibility);
        $disk = Storage::disk($diskInfo['disk']);
        $normalizedPath = $this->normalizePath($path);
        $searchLower = Str::lower(trim($search));

        if ($normalizedPath !== '' && ! $disk->directoryExists($normalizedPath)) {
            $normalizedPath = '';
        }

        $rawDirectories = $disk->directories($normalizedPath);
        sort($rawDirectories, SORT_NATURAL | SORT_FLAG_CASE);

        $directories = [];
        foreach ($rawDirectories as $dirPath) {
            $name = basename($dirPath);
            if ($searchLower !== '' && ! str_contains(Str::lower($name), $searchLower)) {
                continue;
            }

            $directories[] = [
                'name' => $name,
                'path' => $dirPath,
                'type' => 'folder',
                'last_modified' => $this->safeLastModified($disk, $dirPath),
            ];
        }

        $rawFiles = $disk->files($normalizedPath);
        sort($rawFiles, SORT_NATURAL | SORT_FLAG_CASE);

        $files = [];
        $totalSize = 0;

        foreach ($rawFiles as $filePath) {
            $name = basename($filePath);
            if ($name === '.gitignore') {
                continue;
            }

            if ($searchLower !== '' && ! str_contains(Str::lower($name), $searchLower)) {
                continue;
            }

            $size = (int) $disk->size($filePath);
            $totalSize += $size;
            $extension = Str::lower(pathinfo($name, PATHINFO_EXTENSION));
            $mimeType = $this->safeMimeType($disk, $filePath);

            $files[] = [
                'name' => $name,
                'path' => $filePath,
                'type' => 'file',
                'extension' => $extension,
                'size' => $size,
                'size_formatted' => $this->formatBytes($size),
                'mime_type' => $mimeType,
                'is_image' => in_array($extension, self::IMAGE_EXTENSIONS, true),
                'is_editable' => in_array($extension, self::EDITABLE_EXTENSIONS, true) && $size <= self::MAX_EDITABLE_BYTES,
                'public_url' => $diskInfo['visibility'] === 'public' ? $disk->url($filePath) : null,
                'last_modified' => $this->safeLastModified($disk, $filePath),
            ];
        }

        return [
            'visibility' => $diskInfo['visibility'],
            'disk' => $diskInfo['disk'],
            'disk_label' => $diskInfo['label'],
            'root_label' => $diskInfo['root_label'],
            'path' => $normalizedPath,
            'parent_path' => $this->parentPath($normalizedPath),
            'breadcrumbs' => $this->buildBreadcrumbs($normalizedPath),
            'directories' => $directories,
            'files' => $files,
            'stats' => [
                'total_folders' => count($directories),
                'total_files' => count($files),
                'total_size' => $totalSize,
                'total_size_formatted' => $this->formatBytes($totalSize),
            ],
        ];
    }

    /**
     * Upload one or more files into the target directory.
     *
     * @param  array<int, UploadedFile>  $uploadedFiles
     * @return list<string>
     */
    public function uploadFiles(string $visibility, string $directory, array $uploadedFiles, bool $overwrite = false): array
    {
        $diskInfo = $this->resolveDisk($visibility);
        $disk = Storage::disk($diskInfo['disk']);
        $normalizedDir = $this->normalizePath($directory);

        if ($normalizedDir !== '' && ! $disk->directoryExists($normalizedDir)) {
            $disk->makeDirectory($normalizedDir);
        }

        $storedPaths = [];

        foreach ($uploadedFiles as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $originalName = $this->sanitizeName($file->getClientOriginalName());
            $targetName = $overwrite ? $originalName : $this->uniqueFilename($disk, $normalizedDir, $originalName);

            $storedPath = $disk->putFileAs($normalizedDir, $file, $targetName);
            if ($storedPath !== false) {
                $storedPaths[] = $storedPath;
            }
        }

        return $storedPaths;
    }

    /**
     * Create a new folder inside the target directory.
     */
    public function createFolder(string $visibility, string $directory, string $folderName): string
    {
        $diskInfo = $this->resolveDisk($visibility);
        $disk = Storage::disk($diskInfo['disk']);
        $normalizedDir = $this->normalizePath($directory);
        $safeName = $this->sanitizeName($folderName);

        $targetPath = $this->joinPath($normalizedDir, $safeName);

        if ($disk->exists($targetPath) || $disk->directoryExists($targetPath)) {
            throw new InvalidArgumentException(__('A file or folder with that name already exists.'));
        }

        $disk->makeDirectory($targetPath);

        return $targetPath;
    }

    /**
     * Create a new text file inside the target directory.
     */
    public function createFile(string $visibility, string $directory, string $filename, string $content = ''): string
    {
        $diskInfo = $this->resolveDisk($visibility);
        $disk = Storage::disk($diskInfo['disk']);
        $normalizedDir = $this->normalizePath($directory);
        $safeName = $this->sanitizeName($filename);
        $extension = Str::lower(pathinfo($safeName, PATHINFO_EXTENSION));

        if (! in_array($extension, self::EDITABLE_EXTENSIONS, true)) {
            throw new InvalidArgumentException(__('Only text-based file extensions can be created directly.'));
        }

        $targetPath = $this->joinPath($normalizedDir, $safeName);

        if ($disk->exists($targetPath) || $disk->directoryExists($targetPath)) {
            throw new InvalidArgumentException(__('A file or folder with that name already exists.'));
        }

        $disk->put($targetPath, $content);

        return $targetPath;
    }

    /**
     * Rename a file or folder within the same parent directory.
     */
    public function renameItem(string $visibility, string $path, string $newName): string
    {
        $diskInfo = $this->resolveDisk($visibility);
        $disk = Storage::disk($diskInfo['disk']);
        $normalizedPath = $this->normalizePath($path);

        if ($normalizedPath === '') {
            throw new InvalidArgumentException(__('Root directory cannot be renamed.'));
        }

        $isDirectory = $disk->directoryExists($normalizedPath);
        $isFile = $disk->fileExists($normalizedPath);

        if (! $isDirectory && ! $isFile) {
            throw new InvalidArgumentException(__('Target file or folder was not found.'));
        }

        $safeNewName = $this->sanitizeName($newName);
        $parent = $this->parentPath($normalizedPath) ?? '';
        $newPath = $this->joinPath($parent, $safeNewName);

        if ($newPath === $normalizedPath) {
            return $newPath;
        }

        if ($disk->exists($newPath) || $disk->directoryExists($newPath)) {
            throw new InvalidArgumentException(__('A file or folder with that name already exists.'));
        }

        $disk->move($normalizedPath, $newPath);

        return $newPath;
    }

    /**
     * Read an editable text file's metadata and content.
     *
     * @return array{visibility: string, disk: string, path: string, name: string, extension: string, size_formatted: string, content: string, parent_path: string}
     */
    public function readEditableFile(string $visibility, string $path): array
    {
        $diskInfo = $this->resolveDisk($visibility);
        $disk = Storage::disk($diskInfo['disk']);
        $normalizedPath = $this->normalizePath($path);

        if ($normalizedPath === '' || ! $disk->fileExists($normalizedPath)) {
            throw new InvalidArgumentException(__('File was not found.'));
        }

        $name = basename($normalizedPath);
        $extension = Str::lower(pathinfo($name, PATHINFO_EXTENSION));
        $size = (int) $disk->size($normalizedPath);

        if (! in_array($extension, self::EDITABLE_EXTENSIONS, true)) {
            throw new InvalidArgumentException(__('This file type cannot be edited in the text editor.'));
        }

        if ($size > self::MAX_EDITABLE_BYTES) {
            throw new InvalidArgumentException(__('File exceeds the 1 MB editor limit.'));
        }

        return [
            'visibility' => $diskInfo['visibility'],
            'disk' => $diskInfo['disk'],
            'path' => $normalizedPath,
            'name' => $name,
            'extension' => $extension,
            'size_formatted' => $this->formatBytes($size),
            'content' => (string) $disk->get($normalizedPath),
            'parent_path' => $this->parentPath($normalizedPath) ?? '',
        ];
    }

    /**
     * Update the content of an existing editable text file.
     */
    public function updateFileContent(string $visibility, string $path, string $content): void
    {
        $diskInfo = $this->resolveDisk($visibility);
        $disk = Storage::disk($diskInfo['disk']);
        $normalizedPath = $this->normalizePath($path);

        if ($normalizedPath === '' || ! $disk->fileExists($normalizedPath)) {
            throw new InvalidArgumentException(__('File was not found.'));
        }

        $extension = Str::lower(pathinfo($normalizedPath, PATHINFO_EXTENSION));
        if (! in_array($extension, self::EDITABLE_EXTENSIONS, true)) {
            throw new InvalidArgumentException(__('This file type cannot be edited in the text editor.'));
        }

        $disk->put($normalizedPath, $content);
    }

    /**
     * Stream a file download response.
     */
    public function downloadFile(string $visibility, string $path): StreamedResponse
    {
        $diskInfo = $this->resolveDisk($visibility);
        $disk = Storage::disk($diskInfo['disk']);
        $normalizedPath = $this->normalizePath($path);

        if ($normalizedPath === '' || ! $disk->fileExists($normalizedPath)) {
            abort(404, __('File not found.'));
        }

        return $disk->download($normalizedPath);
    }

    /**
     * Stream a file inline for authorized preview (supports private storage).
     */
    public function previewFile(string $visibility, string $path): StreamedResponse
    {
        $diskInfo = $this->resolveDisk($visibility);
        $disk = Storage::disk($diskInfo['disk']);
        $normalizedPath = $this->normalizePath($path);

        if ($normalizedPath === '' || ! $disk->fileExists($normalizedPath)) {
            abort(404, __('File not found.'));
        }

        return $disk->response($normalizedPath);
    }

    /**
     * Delete a file or folder (recursively) from storage.
     */
    public function deleteItem(string $visibility, string $path): bool
    {
        $diskInfo = $this->resolveDisk($visibility);
        $disk = Storage::disk($diskInfo['disk']);
        $normalizedPath = $this->normalizePath($path);

        if ($normalizedPath === '') {
            throw new InvalidArgumentException(__('Root directory cannot be deleted.'));
        }

        if ($disk->directoryExists($normalizedPath)) {
            return $disk->deleteDirectory($normalizedPath);
        }

        if ($disk->fileExists($normalizedPath)) {
            return $disk->delete($normalizedPath);
        }

        return false;
    }

    /**
     * Format bytes into a human-readable string.
     */
    public function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        return round($bytes / (1024 ** $power), 2).' '.$units[$power];
    }

    /**
     * Build clickable breadcrumb items for the given relative path.
     *
     * @return list<array{name: string, path: string}>
     */
    private function buildBreadcrumbs(string $path): array
    {
        $breadcrumbs = [
            ['name' => __('Root'), 'path' => ''],
        ];

        if ($path === '') {
            return $breadcrumbs;
        }

        $acc = [];
        foreach (explode('/', $path) as $segment) {
            $acc[] = $segment;
            $breadcrumbs[] = [
                'name' => $segment,
                'path' => implode('/', $acc),
            ];
        }

        return $breadcrumbs;
    }

    private function parentPath(string $path): ?string
    {
        if ($path === '') {
            return null;
        }

        $dir = dirname($path);

        return $dir === '.' ? '' : $dir;
    }

    private function joinPath(string $directory, string $name): string
    {
        return $directory === '' ? $name : $directory.'/'.$name;
    }

    private function uniqueFilename(Filesystem $disk, string $directory, string $filename): string
    {
        $target = $this->joinPath($directory, $filename);
        if (! $disk->exists($target)) {
            return $filename;
        }

        $base = pathinfo($filename, PATHINFO_FILENAME);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $counter = 1;

        do {
            $candidate = $ext !== '' ? "{$base}-{$counter}.{$ext}" : "{$base}-{$counter}";
            $counter++;
        } while ($disk->exists($this->joinPath($directory, $candidate)));

        return $candidate;
    }

    private function safeLastModified(Filesystem $disk, string $path): string
    {
        try {
            $timestamp = $disk->lastModified($path);

            return Carbon::createFromTimestamp($timestamp)->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return '-';
        }
    }

    private function safeMimeType(Filesystem $disk, string $path): string
    {
        try {
            return (string) ($disk->mimeType($path) ?: 'application/octet-stream');
        } catch (\Throwable) {
            return 'application/octet-stream';
        }
    }
}
