<?php

namespace App\Http\Controllers;

use App\Models\MediaFile;
use App\Models\MediaFileVersion;
use App\Models\MediaFolder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    /**
     * Display the media file manager and storage usage monitoring.
     */
    public function index(Request $request): View
    {
        Gate::authorize('media.view');

        $folderId = $request->filled('folder') ? (int) $request->query('folder') : null;
        $search = trim((string) $request->query('q', ''));
        $type = trim((string) $request->query('type', ''));
        $visibility = trim((string) $request->query('visibility', ''));

        $currentFolder = $folderId ? MediaFolder::query()->with('parent')->find($folderId) : null;

        $breadcrumbs = [];
        $cursor = $currentFolder;
        while ($cursor) {
            array_unshift($breadcrumbs, $cursor);
            $cursor = $cursor->parent;
        }

        $folders = MediaFolder::query()
            ->withCount('files')
            ->where('parent_id', $currentFolder?->id)
            ->orderBy('name')
            ->get();

        $allFolders = MediaFolder::query()->orderBy('name')->get();

        $files = MediaFile::query()
            ->accessibleBy($request->user())
            ->with(['uploader', 'folder', 'versions.creator'])
            ->when($search === '' && $type === '' && $visibility === '', function ($q) use ($currentFolder): void {
                $q->where('folder_id', $currentFolder?->id);
            })
            ->when($search !== '', function ($q) use ($search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($type === 'image', fn ($q) => $q->where('mime_type', 'like', 'image/%'))
            ->when($type === 'document', fn ($q) => $q->where(function ($sub): void {
                $sub->where('mime_type', 'like', 'application/%')
                    ->orWhere('mime_type', 'like', 'text/%');
            }))
            ->when($type === 'other', fn ($q) => $q->where('mime_type', 'not like', 'image/%')
                ->where('mime_type', 'not like', 'application/%')
                ->where('mime_type', 'not like', 'text/%'))
            ->when(in_array($visibility, ['public', 'private', 'role'], true), fn ($q) => $q->where('visibility', $visibility))
            ->latest('id')
            ->get();

        // Storage Usage Monitoring
        $allFilesQuery = MediaFile::query();
        $totalFiles = (clone $allFilesQuery)->count();
        $totalActiveSize = (int) (clone $allFilesQuery)->sum('size');
        $totalVersionsSize = (int) MediaFileVersion::query()->sum('size');
        $imageSize = (int) (clone $allFilesQuery)->where('mime_type', 'like', 'image/%')->sum('size');
        $documentSize = (int) (clone $allFilesQuery)->where(function ($q): void {
            $q->where('mime_type', 'like', 'application/%')->orWhere('mime_type', 'like', 'text/%');
        })->sum('size');
        $otherSize = max(0, $totalActiveSize - $imageSize - $documentSize);

        $quotaBytes = 1073741824; // 1 GB default monitoring quota
        $usagePercent = $quotaBytes > 0 ? min(100, round(($totalVersionsSize / $quotaBytes) * 100, 1)) : 0;

        $storageStats = [
            'total_files' => $totalFiles,
            'total_folders' => MediaFolder::query()->count(),
            'active_size' => $totalActiveSize,
            'versions_size' => $totalVersionsSize,
            'image_size' => $imageSize,
            'document_size' => $documentSize,
            'other_size' => $otherSize,
            'quota_bytes' => $quotaBytes,
            'usage_percent' => $usagePercent,
        ];

        $roles = Role::query()->orderBy('name')->pluck('name');

        return view('modules.media.index', [
            'currentFolder' => $currentFolder,
            'breadcrumbs' => $breadcrumbs,
            'folders' => $folders,
            'allFolders' => $allFolders,
            'files' => $files,
            'storageStats' => $storageStats,
            'roles' => $roles,
            'search' => $search,
            'type' => $type,
            'visibility' => $visibility,
        ]);
    }

    /**
     * Handle multiple / drag-and-drop file uploads.
     */
    public function upload(Request $request): RedirectResponse|JsonResponse
    {
        Gate::authorize('media.create');

        $validated = $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'file', 'max:20480'],
            'folder_id' => ['nullable', 'integer', Rule::exists('media_folders', 'id')],
            'visibility' => ['nullable', 'string', Rule::in(['public', 'private', 'role'])],
        ]);

        $disk = 'public';
        $folderPath = 'media/'.date('Y/m');
        $uploaded = [];

        foreach ($request->file('files') as $file) {
            $storedPath = $file->store($folderPath, $disk);
            $mime = (string) ($file->getMimeType() ?: $file->getClientMimeType() ?: 'application/octet-stream');
            $ext = strtolower($file->getClientOriginalExtension());
            $size = (int) $file->getSize();
            [$width, $height] = $this->extractImageDimensions(Storage::disk($disk)->path($storedPath), $mime);

            $media = MediaFile::query()->create([
                'folder_id' => $validated['folder_id'] ?? null,
                'name' => $file->getClientOriginalName(),
                'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'disk' => $disk,
                'path' => $storedPath,
                'mime_type' => $mime,
                'extension' => $ext,
                'size' => $size,
                'width' => $width,
                'height' => $height,
                'visibility' => $validated['visibility'] ?? 'public',
                'current_version' => 1,
                'uploaded_by' => $request->user()?->id,
            ]);

            MediaFileVersion::query()->create([
                'media_file_id' => $media->id,
                'version_number' => 1,
                'disk' => $disk,
                'path' => $storedPath,
                'mime_type' => $mime,
                'size' => $size,
                'width' => $width,
                'height' => $height,
                'note' => __('Initial upload'),
                'created_by' => $request->user()?->id,
            ]);

            $uploaded[] = $media;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __(':count file(s) uploaded successfully.', ['count' => count($uploaded)]),
                'files' => $uploaded,
            ], 201);
        }

        return back()->with('success', __(':count file(s) uploaded successfully.', ['count' => count($uploaded)]));
    }

    /**
     * Update file metadata and access permissions.
     */
    public function updateMetadata(Request $request, MediaFile $mediaFile): RedirectResponse|JsonResponse
    {
        Gate::authorize('media.edit');
        abort_unless($mediaFile->canBeAccessedBy($request->user()), 403);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'tags' => ['nullable', 'string', 'max:500'],
            'folder_id' => ['nullable', 'integer', Rule::exists('media_folders', 'id')],
            'visibility' => ['required', 'string', Rule::in(['public', 'private', 'role'])],
            'allowed_roles' => ['nullable', 'array'],
            'allowed_roles.*' => ['string'],
        ]);

        $tags = collect(explode(',', (string) ($validated['tags'] ?? '')))
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $mediaFile->update([
            'title' => $validated['title'] ?: $mediaFile->name,
            'alt_text' => $validated['alt_text'] ?? null,
            'description' => $validated['description'] ?? null,
            'tags' => $tags,
            'folder_id' => $validated['folder_id'] ?? null,
            'visibility' => $validated['visibility'],
            'allowed_roles' => $validated['visibility'] === 'role' ? ($validated['allowed_roles'] ?? []) : [],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('File metadata & permissions updated successfully.'),
                'file' => $mediaFile->fresh(),
            ]);
        }

        return back()->with('success', __('File metadata & permissions updated successfully.'));
    }

    /**
     * Upload a new version for an existing file.
     */
    public function uploadVersion(Request $request, MediaFile $mediaFile): RedirectResponse|JsonResponse
    {
        Gate::authorize('media.edit');
        abort_unless($mediaFile->canBeAccessedBy($request->user()), 403);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:20480'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('file');
        $disk = $mediaFile->disk ?: 'public';
        $storedPath = $file->store('media/'.date('Y/m'), $disk);
        $mime = (string) ($file->getMimeType() ?: $file->getClientMimeType() ?: 'application/octet-stream');
        $ext = strtolower($file->getClientOriginalExtension());
        $size = (int) $file->getSize();
        [$width, $height] = $this->extractImageDimensions(Storage::disk($disk)->path($storedPath), $mime);

        $nextVersion = ((int) $mediaFile->current_version) + 1;

        MediaFileVersion::query()->create([
            'media_file_id' => $mediaFile->id,
            'version_number' => $nextVersion,
            'disk' => $disk,
            'path' => $storedPath,
            'mime_type' => $mime,
            'size' => $size,
            'width' => $width,
            'height' => $height,
            'note' => $validated['note'] ?? __('Uploaded version :v', ['v' => $nextVersion]),
            'created_by' => $request->user()?->id,
        ]);

        $mediaFile->update([
            'name' => $file->getClientOriginalName(),
            'path' => $storedPath,
            'mime_type' => $mime,
            'extension' => $ext,
            'size' => $size,
            'width' => $width,
            'height' => $height,
            'current_version' => $nextVersion,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('New file version :v uploaded.', ['v' => $nextVersion]),
            ]);
        }

        return back()->with('success', __('New file version :v uploaded.', ['v' => $nextVersion]));
    }

    /**
     * Restore a previous version as the active version.
     */
    public function restoreVersion(Request $request, MediaFile $mediaFile, MediaFileVersion $version): RedirectResponse|JsonResponse
    {
        Gate::authorize('media.edit');
        abort_unless((int) $version->media_file_id === (int) $mediaFile->id, 404);
        abort_unless($mediaFile->canBeAccessedBy($request->user()), 403);

        $nextVersion = ((int) $mediaFile->current_version) + 1;

        MediaFileVersion::query()->create([
            'media_file_id' => $mediaFile->id,
            'version_number' => $nextVersion,
            'disk' => $version->disk,
            'path' => $version->path,
            'mime_type' => $version->mime_type,
            'size' => $version->size,
            'width' => $version->width,
            'height' => $version->height,
            'note' => __('Restored from v:v', ['v' => $version->version_number]),
            'created_by' => $request->user()?->id,
        ]);

        $mediaFile->update([
            'path' => $version->path,
            'mime_type' => $version->mime_type,
            'size' => $version->size,
            'width' => $version->width,
            'height' => $version->height,
            'current_version' => $nextVersion,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Restored version v:v.', ['v' => $version->version_number]),
            ]);
        }

        return back()->with('success', __('Restored version v:v.', ['v' => $version->version_number]));
    }

    /**
     * Save a cropped image from CropperJS as a new file version.
     */
    public function crop(Request $request, MediaFile $mediaFile): JsonResponse
    {
        Gate::authorize('media.edit');
        abort_unless($mediaFile->canBeAccessedBy($request->user()), 403);
        abort_unless($mediaFile->isImage(), 422, __('Only image files can be cropped.'));

        $validated = $request->validate([
            'cropped_image' => ['required', 'string'],
        ]);

        if (! preg_match('/^data:image\/(\w+);base64,(.+)$/', $validated['cropped_image'], $matches)) {
            return response()->json(['message' => __('Invalid image payload.')], 422);
        }

        $ext = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
        $binary = base64_decode($matches[2], true);

        if ($binary === false) {
            return response()->json(['message' => __('Failed to decode cropped image.')], 422);
        }

        $disk = $mediaFile->disk ?: 'public';
        $storedPath = 'media/'.date('Y/m').'/'.Str::random(24).'.'.$ext;
        Storage::disk($disk)->put($storedPath, $binary);

        $fullPath = Storage::disk($disk)->path($storedPath);
        $mime = 'image/'.($ext === 'jpg' ? 'jpeg' : $ext);
        $size = strlen($binary);
        [$width, $height] = $this->extractImageDimensions($fullPath, $mime);

        $nextVersion = ((int) $mediaFile->current_version) + 1;

        MediaFileVersion::query()->create([
            'media_file_id' => $mediaFile->id,
            'version_number' => $nextVersion,
            'disk' => $disk,
            'path' => $storedPath,
            'mime_type' => $mime,
            'size' => $size,
            'width' => $width,
            'height' => $height,
            'note' => __('Cropped image'),
            'created_by' => $request->user()?->id,
        ]);

        $mediaFile->update([
            'path' => $storedPath,
            'mime_type' => $mime,
            'extension' => $ext,
            'size' => $size,
            'width' => $width,
            'height' => $height,
            'current_version' => $nextVersion,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Image cropped and saved as v:v.', ['v' => $nextVersion]),
        ]);
    }

    /**
     * Compress and optionally resize an image using GD and save as a new version.
     */
    public function compress(Request $request, MediaFile $mediaFile): JsonResponse
    {
        Gate::authorize('media.edit');
        abort_unless($mediaFile->canBeAccessedBy($request->user()), 403);
        abort_unless($mediaFile->isImage(), 422, __('Only image files can be compressed.'));

        if (! extension_loaded('gd')) {
            return response()->json(['message' => __('GD extension is not enabled on the server.')], 500);
        }

        $validated = $request->validate([
            'quality' => ['required', 'integer', 'min:10', 'max:100'],
            'max_width' => ['nullable', 'integer', 'min:100', 'max:8000'],
            'format' => ['nullable', 'string', Rule::in(['original', 'jpg', 'webp'])],
        ]);

        $disk = $mediaFile->disk ?: 'public';
        $sourcePath = Storage::disk($disk)->path($mediaFile->path);
        if (! is_file($sourcePath)) {
            return response()->json(['message' => __('Source file not found.')], 404);
        }

        $raw = file_get_contents($sourcePath);
        $srcImage = $raw !== false ? @imagecreatefromstring($raw) : false;
        if (! $srcImage) {
            return response()->json(['message' => __('Unable to process this image format.')], 422);
        }

        $origW = imagesx($srcImage);
        $origH = imagesy($srcImage);
        $maxW = isset($validated['max_width']) ? (int) $validated['max_width'] : null;

        if ($maxW && $origW > $maxW) {
            $newW = $maxW;
            $newH = (int) round(($origH / $origW) * $newW);
            $resized = imagecreatetruecolor($newW, $newH);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $srcImage, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($srcImage);
            $srcImage = $resized;
        }

        $targetFormat = $validated['format'] ?? 'original';
        if ($targetFormat === 'original') {
            $targetFormat = match (strtolower((string) $mediaFile->extension)) {
                'png' => 'png',
                'webp' => 'webp',
                default => 'jpg',
            };
        }

        $quality = (int) $validated['quality'];
        $storedPath = 'media/'.date('Y/m').'/'.Str::random(24).'.'.$targetFormat;
        $destFullPath = Storage::disk($disk)->path($storedPath);
        Storage::disk($disk)->makeDirectory(dirname($storedPath));

        $ok = match ($targetFormat) {
            'webp' => function_exists('imagewebp') ? imagewebp($srcImage, $destFullPath, $quality) : imagejpeg($srcImage, $destFullPath, $quality),
            'png' => imagepng($srcImage, $destFullPath, (int) round((100 - $quality) / 11)),
            default => imagejpeg($srcImage, $destFullPath, $quality),
        };

        $finalW = imagesx($srcImage);
        $finalH = imagesy($srcImage);
        imagedestroy($srcImage);

        if (! $ok || ! is_file($destFullPath)) {
            return response()->json(['message' => __('Compression failed.')], 500);
        }

        $newSize = (int) filesize($destFullPath);
        $oldSize = (int) $mediaFile->size;
        $mime = match ($targetFormat) {
            'webp' => 'image/webp',
            'png' => 'image/png',
            default => 'image/jpeg',
        };

        $nextVersion = ((int) $mediaFile->current_version) + 1;

        MediaFileVersion::query()->create([
            'media_file_id' => $mediaFile->id,
            'version_number' => $nextVersion,
            'disk' => $disk,
            'path' => $storedPath,
            'mime_type' => $mime,
            'size' => $newSize,
            'width' => $finalW,
            'height' => $finalH,
            'note' => __('Compressed (:quality%)', ['quality' => $quality]),
            'created_by' => $request->user()?->id,
        ]);

        $mediaFile->update([
            'path' => $storedPath,
            'mime_type' => $mime,
            'extension' => $targetFormat,
            'size' => $newSize,
            'width' => $finalW,
            'height' => $finalH,
            'current_version' => $nextVersion,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Image compressed (v:v). Old: :old, New: :new.', [
                'v' => $nextVersion,
                'old' => number_format($oldSize / 1024, 1).' KB',
                'new' => number_format($newSize / 1024, 1).' KB',
            ]),
        ]);
    }

    /**
     * Toggle or configure public share link for a file.
     */
    public function share(Request $request, MediaFile $mediaFile): JsonResponse
    {
        Gate::authorize('media.edit');
        abort_unless($mediaFile->canBeAccessedBy($request->user()), 403);

        $validated = $request->validate([
            'is_shared' => ['required', 'boolean'],
            'share_expires_at' => ['nullable', 'date'],
        ]);

        $token = $mediaFile->share_token ?: Str::random(40);

        $mediaFile->update([
            'is_shared' => (bool) $validated['is_shared'],
            'share_token' => $token,
            'share_expires_at' => $validated['share_expires_at'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'is_shared' => $mediaFile->is_shared,
            'share_url' => $mediaFile->is_shared ? route('media.shared', $mediaFile->share_token) : null,
            'message' => $mediaFile->is_shared ? __('Share link enabled.') : __('Share link disabled.'),
        ]);
    }

    /**
     * Publicly view or download a shared file via token.
     */
    public function shared(Request $request, string $token): View|StreamedResponse|BinaryFileResponse
    {
        $mediaFile = MediaFile::query()
            ->where('share_token', $token)
            ->where('is_shared', true)
            ->firstOrFail();

        if ($mediaFile->share_expires_at && $mediaFile->share_expires_at->isPast()) {
            abort(410, __('This share link has expired.'));
        }

        if ($request->boolean('download')) {
            return Storage::disk($mediaFile->disk ?: 'public')->download($mediaFile->path, $mediaFile->name);
        }

        return view('modules.media.shared', [
            'file' => $mediaFile,
        ]);
    }

    /**
     * Download a media file after checking access permissions.
     */
    public function download(Request $request, MediaFile $mediaFile): StreamedResponse|BinaryFileResponse
    {
        Gate::authorize('media.view');
        abort_unless($mediaFile->canBeAccessedBy($request->user()), 403);

        return Storage::disk($mediaFile->disk ?: 'public')->download($mediaFile->path, $mediaFile->name);
    }

    /**
     * Delete a media file and all its stored versions.
     */
    public function destroy(Request $request, MediaFile $mediaFile): RedirectResponse|JsonResponse
    {
        Gate::authorize('media.delete');
        abort_unless($mediaFile->canBeAccessedBy($request->user()), 403);

        $disk = $mediaFile->disk ?: 'public';
        $paths = $mediaFile->versions()->pluck('path')->push($mediaFile->path)->unique()->filter();

        foreach ($paths as $path) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }

        $mediaFile->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('File deleted successfully.'),
            ]);
        }

        return back()->with('success', __('File deleted successfully.'));
    }

    /**
     * Extract width and height if the file is an image.
     *
     * @return array{0: int|null, 1: int|null}
     */
    private function extractImageDimensions(string $fullPath, string $mime): array
    {
        if (! str_starts_with($mime, 'image/') || ! is_file($fullPath)) {
            return [null, null];
        }

        $info = @getimagesize($fullPath);
        if (! is_array($info)) {
            return [null, null];
        }

        return [(int) $info[0], (int) $info[1]];
    }
}
