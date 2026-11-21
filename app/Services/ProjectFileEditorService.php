<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ProjectFileEditorService
{
    /**
     * Maximum file size (2 MB) allowed for editing.
     */
    private const MAX_EDITABLE_BYTES = 2097152;

    /**
     * Directories hidden from listing to prevent repository corruption.
     *
     * @var list<string>
     */
    private const HIDDEN_TOP_LEVEL = ['.git'];

    /**
     * Text/code extensions supported by the editor.
     *
     * @var list<string>
     */
    private const EDITABLE_EXTENSIONS = [
        'php', 'js', 'ts', 'jsx', 'tsx', 'vue', 'json', 'css', 'scss', 'sass', 'less',
        'html', 'htm', 'xml', 'svg', 'md', 'txt', 'log', 'sql', 'yml', 'yaml',
        'ini', 'conf', 'sh', 'bat', 'env', 'example', 'csv', 'stub', 'lock',
    ];

    /**
     * Extensionless or dotfile names that are editable as plain text.
     *
     * @var list<string>
     */
    private const EDITABLE_FILENAMES = [
        '.env', '.env.example', '.env.testing', '.gitignore', '.gitattributes',
        '.editorconfig', '.htaccess', 'artisan', 'phpunit.xml', 'readme',
    ];

    /**
     * List directories and files from the project root or any nested subdirectory.
     *
     * @return array<string, mixed>
     */
    public function listDirectory(string $path = '', string $search = ''): array
    {
        $root = $this->rootPath();
        $normalized = $this->normalizeRelativePath($path);
        $fullPath = $normalized === '' ? $root : $root.DIRECTORY_SEPARATOR.$normalized;

        if (! is_dir($fullPath)) {
            $normalized = '';
            $fullPath = $root;
        }

        $realDir = realpath($fullPath);
        if ($realDir === false || ! $this->isWithinRoot($realDir, $root)) {
            throw new InvalidArgumentException(__('Access outside project root is not allowed.'));
        }

        $searchLower = Str::lower(trim($search));
        $entries = @scandir($realDir) ?: [];

        $directories = [];
        $files = [];
        $totalSize = 0;

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            if ($normalized === '' && in_array($entry, self::HIDDEN_TOP_LEVEL, true)) {
                continue;
            }

            $entryReal = realpath($realDir.DIRECTORY_SEPARATOR.$entry);
            if ($entryReal === false || ! $this->isWithinRoot($entryReal, $root)) {
                continue;
            }

            if ($searchLower !== '' && ! str_contains(Str::lower($entry), $searchLower)) {
                continue;
            }

            $relPath = $this->joinRelativePath($normalized, $entry);
            $modified = @filemtime($entryReal);
            $lastModified = $modified !== false
                ? Carbon::createFromTimestamp($modified)->format('Y-m-d H:i:s')
                : '-';

            if (is_dir($entryReal)) {
                $directories[] = [
                    'name' => $entry,
                    'path' => $relPath,
                    'last_modified' => $lastModified,
                ];
            } elseif (is_file($entryReal)) {
                $size = (int) (@filesize($entryReal) ?: 0);
                $totalSize += $size;
                $ext = Str::lower(pathinfo($entry, PATHINFO_EXTENSION));

                $files[] = [
                    'name' => $entry,
                    'path' => $relPath,
                    'extension' => $ext,
                    'size' => $size,
                    'size_formatted' => $this->formatBytes($size),
                    'is_editable' => $this->isEditableFile($entry, $ext, $size),
                    'last_modified' => $lastModified,
                ];
            }
        }

        usort($directories, static fn (array $a, array $b): int => strnatcasecmp($a['name'], $b['name']));
        usort($files, static fn (array $a, array $b): int => strnatcasecmp($a['name'], $b['name']));

        return [
            'root_path' => $root,
            'path' => $normalized,
            'parent_path' => $this->parentRelativePath($normalized),
            'breadcrumbs' => $this->buildBreadcrumbs($normalized),
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
     * Read an editable project file.
     *
     * @return array<string, mixed>
     */
    public function readFile(string $relativePath): array
    {
        $resolved = $this->resolveExistingFile($relativePath);
        $name = basename($resolved['relative']);
        $ext = Str::lower(pathinfo($name, PATHINFO_EXTENSION));
        $size = (int) (@filesize($resolved['real']) ?: 0);

        if ($size > self::MAX_EDITABLE_BYTES) {
            throw new InvalidArgumentException(__('File exceeds the 2 MB editor limit.'));
        }

        if (! $this->isEditableFile($name, $ext, $size)) {
            throw new InvalidArgumentException(__('This file type is not supported in the text editor.'));
        }

        $content = (string) File::get($resolved['real']);
        if (str_contains($content, "\0")) {
            throw new InvalidArgumentException(__('Binary files cannot be opened in the text editor.'));
        }

        $modified = @filemtime($resolved['real']);

        return [
            'name' => $name,
            'path' => $resolved['relative'],
            'parent_path' => $this->parentRelativePath($resolved['relative']) ?? '',
            'extension' => $ext,
            'size' => $size,
            'size_formatted' => $this->formatBytes($size),
            'last_modified' => $modified !== false ? Carbon::createFromTimestamp($modified)->format('Y-m-d H:i:s') : '-',
            'line_count' => substr_count($content, "\n") + 1,
            'content' => $content,
        ];
    }

    /**
     * Write updated content to an existing project file.
     *
     * @return array{before_size: int, after_size: int}
     */
    public function updateFile(string $relativePath, string $content): array
    {
        $resolved = $this->resolveExistingFile($relativePath);
        $name = basename($resolved['relative']);
        $ext = Str::lower(pathinfo($name, PATHINFO_EXTENSION));
        $beforeSize = (int) (@filesize($resolved['real']) ?: 0);

        if (! $this->isEditableFile($name, $ext, $beforeSize)) {
            throw new InvalidArgumentException(__('This file type is not supported in the text editor.'));
        }

        File::put($resolved['real'], $content);
        clearstatcache(true, $resolved['real']);
        $afterSize = (int) (@filesize($resolved['real']) ?: 0);

        return [
            'before_size' => $beforeSize,
            'after_size' => $afterSize,
        ];
    }

    /**
     * Create a new file inside the given project directory.
     */
    public function createFile(string $directory, string $filename, string $content = ''): string
    {
        $root = $this->rootPath();
        $dirRel = $this->normalizeRelativePath($directory);
        $safeName = $this->sanitizeSingleName($filename);

        if ($dirRel === '' && in_array($safeName, self::HIDDEN_TOP_LEVEL, true)) {
            throw new InvalidArgumentException(__('This filename is reserved.'));
        }

        $targetRel = $this->joinRelativePath($dirRel, $safeName);
        $parentReal = $dirRel === '' ? $root : realpath($root.DIRECTORY_SEPARATOR.$dirRel);

        if ($parentReal === false || ! is_dir($parentReal) || ! $this->isWithinRoot($parentReal, $root)) {
            throw new InvalidArgumentException(__('Target directory does not exist.'));
        }

        $targetFull = $parentReal.DIRECTORY_SEPARATOR.$safeName;
        if (file_exists($targetFull)) {
            throw new InvalidArgumentException(__('A file or folder with that name already exists.'));
        }

        File::put($targetFull, $content);

        return $targetRel;
    }

    /**
     * Create a new folder inside the given project directory.
     */
    public function createFolder(string $directory, string $folderName): string
    {
        $root = $this->rootPath();
        $dirRel = $this->normalizeRelativePath($directory);
        $safeName = $this->sanitizeSingleName($folderName);

        if ($dirRel === '' && in_array($safeName, self::HIDDEN_TOP_LEVEL, true)) {
            throw new InvalidArgumentException(__('This folder name is reserved.'));
        }

        $targetRel = $this->joinRelativePath($dirRel, $safeName);
        $parentReal = $dirRel === '' ? $root : realpath($root.DIRECTORY_SEPARATOR.$dirRel);

        if ($parentReal === false || ! is_dir($parentReal) || ! $this->isWithinRoot($parentReal, $root)) {
            throw new InvalidArgumentException(__('Target directory does not exist.'));
        }

        $targetFull = $parentReal.DIRECTORY_SEPARATOR.$safeName;
        if (file_exists($targetFull)) {
            throw new InvalidArgumentException(__('A file or folder with that name already exists.'));
        }

        File::makeDirectory($targetFull, 0755, false);

        return $targetRel;
    }

    /**
     * Rename a file or folder within the same directory.
     */
    public function renameItem(string $relativePath, string $newName): string
    {
        $root = $this->rootPath();
        $normalized = $this->normalizeRelativePath($relativePath);

        if ($normalized === '') {
            throw new InvalidArgumentException(__('Project root cannot be renamed.'));
        }

        if (in_array($normalized, self::HIDDEN_TOP_LEVEL, true)) {
            throw new InvalidArgumentException(__('Protected directory cannot be renamed.'));
        }

        $realSource = realpath($root.DIRECTORY_SEPARATOR.$normalized);
        if ($realSource === false || ! $this->isWithinRoot($realSource, $root)) {
            throw new InvalidArgumentException(__('Target file or folder was not found.'));
        }

        $safeNewName = $this->sanitizeSingleName($newName);
        $parentRel = $this->parentRelativePath($normalized) ?? '';
        $newRel = $this->joinRelativePath($parentRel, $safeNewName);

        if ($newRel === $normalized) {
            return $newRel;
        }

        $targetFull = dirname($realSource).DIRECTORY_SEPARATOR.$safeNewName;
        if (file_exists($targetFull)) {
            throw new InvalidArgumentException(__('A file or folder with that name already exists.'));
        }

        File::move($realSource, $targetFull);

        return $newRel;
    }

    /**
     * Delete a file or folder inside the project root.
     */
    public function deleteItem(string $relativePath): bool
    {
        $root = $this->rootPath();
        $normalized = $this->normalizeRelativePath($relativePath);

        if ($normalized === '' || in_array($normalized, self::HIDDEN_TOP_LEVEL, true)) {
            throw new InvalidArgumentException(__('Protected directory cannot be deleted.'));
        }

        $realTarget = realpath($root.DIRECTORY_SEPARATOR.$normalized);
        if ($realTarget === false || ! $this->isWithinRoot($realTarget, $root) || $realTarget === $root) {
            throw new InvalidArgumentException(__('Target file or folder was not found.'));
        }

        if (is_dir($realTarget)) {
            return File::deleteDirectory($realTarget);
        }

        if (is_file($realTarget)) {
            return File::delete($realTarget);
        }

        return false;
    }

    /**
     * @return array{relative: string, real: string}
     */
    private function resolveExistingFile(string $relativePath): array
    {
        $root = $this->rootPath();
        $normalized = $this->normalizeRelativePath($relativePath);

        if ($normalized === '') {
            throw new InvalidArgumentException(__('Invalid file path.'));
        }

        $real = realpath($root.DIRECTORY_SEPARATOR.$normalized);
        if ($real === false || ! is_file($real) || ! $this->isWithinRoot($real, $root)) {
            throw new InvalidArgumentException(__('File was not found.'));
        }

        return [
            'relative' => $normalized,
            'real' => $real,
        ];
    }

    private function rootPath(): string
    {
        $root = realpath(base_path());

        return $root !== false ? $root : base_path();
    }

    private function isWithinRoot(string $realPath, string $root): bool
    {
        return $realPath === $root || str_starts_with($realPath, $root.DIRECTORY_SEPARATOR);
    }

    private function normalizeRelativePath(?string $path): string
    {
        $path = str_replace('\\', '/', trim((string) $path));

        if (str_contains($path, "\0")) {
            throw new InvalidArgumentException(__('Invalid path character detected.'));
        }

        $segments = array_values(array_filter(explode('/', $path), static fn (string $s): bool => $s !== ''));

        foreach ($segments as $segment) {
            if ($segment === '.' || $segment === '..') {
                throw new InvalidArgumentException(__('Path traversal is not allowed.'));
            }
        }

        return implode('/', $segments);
    }

    private function sanitizeSingleName(string $name): string
    {
        $name = trim(str_replace(['\\', '/'], '', $name));

        if ($name === '' || $name === '.' || $name === '..' || str_contains($name, "\0")) {
            throw new InvalidArgumentException(__('Invalid file or folder name.'));
        }

        return $name;
    }

    private function isEditableFile(string $filename, string $extension, int $size): bool
    {
        if ($size > self::MAX_EDITABLE_BYTES) {
            return false;
        }

        if (in_array(Str::lower($filename), self::EDITABLE_FILENAMES, true)) {
            return true;
        }

        if (str_starts_with(Str::lower($filename), '.env')) {
            return true;
        }

        return in_array($extension, self::EDITABLE_EXTENSIONS, true);
    }

    private function joinRelativePath(string $directory, string $name): string
    {
        return $directory === '' ? $name : $directory.'/'.$name;
    }

    private function parentRelativePath(string $path): ?string
    {
        if ($path === '') {
            return null;
        }

        $parent = dirname($path);

        return $parent === '.' ? '' : $parent;
    }

    /**
     * @return list<array{name: string, path: string}>
     */
    private function buildBreadcrumbs(string $path): array
    {
        $breadcrumbs = [
            ['name' => basename($this->rootPath()), 'path' => ''],
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
