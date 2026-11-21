<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\FileManagerService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileManagerController extends Controller
{
    public function __construct(
        protected FileManagerService $fileManagerService
    ) {}

    /**
     * Display the storage file manager for public and private disks.
     */
    public function index(Request $request): View
    {
        Gate::authorize('file-manager.view');

        $visibility = $request->string('disk')->toString() === 'private' ? 'private' : 'public';
        $path = $request->string('path')->toString();
        $search = $request->string('search')->toString();

        try {
            $listing = $this->fileManagerService->listDirectory($visibility, $path, $search);
        } catch (\InvalidArgumentException $e) {
            $listing = $this->fileManagerService->listDirectory($visibility, '', $search);
            session()->flash('error', $e->getMessage());
        }

        return view('modules.file-manager.index', [
            ...$listing,
            'search' => $search,
        ]);
    }

    /**
     * Upload one or more files into the current storage directory.
     */
    public function upload(Request $request): RedirectResponse
    {
        Gate::authorize('file-manager.create');

        $validated = $request->validate([
            'disk' => ['required', 'in:public,private'],
            'path' => ['nullable', 'string', 'max:500'],
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'file', 'max:20480'],
            'overwrite' => ['nullable', 'boolean'],
        ]);

        $visibility = $validated['disk'];
        $path = (string) ($validated['path'] ?? '');
        $overwrite = (bool) ($validated['overwrite'] ?? false);

        try {
            $storedPaths = $this->fileManagerService->uploadFiles(
                $visibility,
                $path,
                $request->file('files', []),
                $overwrite
            );

            ActivityLog::record(
                module: 'file-manager',
                action: 'uploaded',
                description: sprintf('Uploaded %d file(s) to %s storage (%s)', count($storedPaths), $visibility, $path !== '' ? $path : 'root'),
                subjectLabel: $visibility.':'.($path !== '' ? $path : '/'),
                before: null,
                after: ['disk' => $visibility, 'files' => $storedPaths],
                subjectId: $visibility,
            );

            return redirect()
                ->route('file-manager.index', ['disk' => $visibility, 'path' => $path])
                ->with('success', __(':count file(s) uploaded successfully.', ['count' => count($storedPaths)]));
        } catch (\Throwable $e) {
            return redirect()
                ->route('file-manager.index', ['disk' => $visibility, 'path' => $path])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create a new folder inside the current storage directory.
     */
    public function storeFolder(Request $request): RedirectResponse
    {
        Gate::authorize('file-manager.create');

        $validated = $request->validate([
            'disk' => ['required', 'in:public,private'],
            'path' => ['nullable', 'string', 'max:500'],
            'name' => ['required', 'string', 'max:120'],
        ]);

        $visibility = $validated['disk'];
        $path = (string) ($validated['path'] ?? '');

        try {
            $createdPath = $this->fileManagerService->createFolder($visibility, $path, $validated['name']);

            ActivityLog::record(
                module: 'file-manager',
                action: 'created_folder',
                description: "Created folder {$createdPath} in {$visibility} storage",
                subjectLabel: $createdPath,
                before: null,
                after: ['disk' => $visibility, 'path' => $createdPath],
                subjectId: $createdPath,
            );

            return redirect()
                ->route('file-manager.index', ['disk' => $visibility, 'path' => $path])
                ->with('success', __('Folder created successfully.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('file-manager.index', ['disk' => $visibility, 'path' => $path])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create a new text file inside the current storage directory.
     */
    public function storeFile(Request $request): RedirectResponse
    {
        Gate::authorize('file-manager.create');

        $validated = $request->validate([
            'disk' => ['required', 'in:public,private'],
            'path' => ['nullable', 'string', 'max:500'],
            'name' => ['required', 'string', 'max:120'],
            'content' => ['nullable', 'string'],
        ]);

        $visibility = $validated['disk'];
        $path = (string) ($validated['path'] ?? '');

        try {
            $createdPath = $this->fileManagerService->createFile(
                $visibility,
                $path,
                $validated['name'],
                (string) ($validated['content'] ?? '')
            );

            ActivityLog::record(
                module: 'file-manager',
                action: 'created_file',
                description: "Created file {$createdPath} in {$visibility} storage",
                subjectLabel: $createdPath,
                before: null,
                after: ['disk' => $visibility, 'path' => $createdPath],
                subjectId: $createdPath,
            );

            return redirect()
                ->route('file-manager.index', ['disk' => $visibility, 'path' => $path])
                ->with('success', __('File created successfully.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('file-manager.index', ['disk' => $visibility, 'path' => $path])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Rename an existing file or folder.
     */
    public function rename(Request $request): RedirectResponse
    {
        Gate::authorize('file-manager.edit');

        $validated = $request->validate([
            'disk' => ['required', 'in:public,private'],
            'path' => ['nullable', 'string', 'max:500'],
            'target_path' => ['required', 'string', 'max:500'],
            'new_name' => ['required', 'string', 'max:120'],
        ]);

        $visibility = $validated['disk'];
        $currentPath = (string) ($validated['path'] ?? '');

        try {
            $newPath = $this->fileManagerService->renameItem(
                $visibility,
                $validated['target_path'],
                $validated['new_name']
            );

            ActivityLog::record(
                module: 'file-manager',
                action: 'renamed',
                description: "Renamed {$validated['target_path']} to {$newPath} in {$visibility} storage",
                subjectLabel: $newPath,
                before: ['disk' => $visibility, 'path' => $validated['target_path']],
                after: ['disk' => $visibility, 'path' => $newPath],
                subjectId: $newPath,
            );

            return redirect()
                ->route('file-manager.index', ['disk' => $visibility, 'path' => $currentPath])
                ->with('success', __('Item renamed successfully.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('file-manager.index', ['disk' => $visibility, 'path' => $currentPath])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the text editor for an editable file.
     */
    public function edit(Request $request): View|RedirectResponse
    {
        Gate::authorize('file-manager.edit');

        $visibility = $request->string('disk')->toString() === 'private' ? 'private' : 'public';
        $targetPath = $request->string('target_path')->toString();

        try {
            $file = $this->fileManagerService->readEditableFile($visibility, $targetPath);

            return view('modules.file-manager.edit', $file);
        } catch (\Throwable $e) {
            return redirect()
                ->route('file-manager.index', ['disk' => $visibility])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Save updated content for an editable text file.
     */
    public function updateContent(Request $request): RedirectResponse
    {
        Gate::authorize('file-manager.edit');

        $validated = $request->validate([
            'disk' => ['required', 'in:public,private'],
            'target_path' => ['required', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
        ]);

        $visibility = $validated['disk'];
        $targetPath = $validated['target_path'];

        try {
            $this->fileManagerService->updateFileContent(
                $visibility,
                $targetPath,
                (string) ($validated['content'] ?? '')
            );

            ActivityLog::record(
                module: 'file-manager',
                action: 'updated',
                description: "Updated file {$targetPath} in {$visibility} storage",
                subjectLabel: $targetPath,
                before: null,
                after: ['disk' => $visibility, 'path' => $targetPath],
                subjectId: $targetPath,
            );

            return redirect()
                ->route('file-manager.edit', ['disk' => $visibility, 'target_path' => $targetPath])
                ->with('success', __('File content updated successfully.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('file-manager.index', ['disk' => $visibility])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Stream a file inline for previewing (works for both public and private storage).
     */
    public function preview(Request $request): StreamedResponse
    {
        Gate::authorize('file-manager.view');

        $visibility = $request->string('disk')->toString() === 'private' ? 'private' : 'public';
        $targetPath = $request->string('target_path')->toString();

        return $this->fileManagerService->previewFile($visibility, $targetPath);
    }

    /**
     * Download a file from public or private storage.
     */
    public function download(Request $request): StreamedResponse
    {
        Gate::authorize('file-manager.view');

        $visibility = $request->string('disk')->toString() === 'private' ? 'private' : 'public';
        $targetPath = $request->string('target_path')->toString();

        return $this->fileManagerService->downloadFile($visibility, $targetPath);
    }

    /**
     * Delete a file or folder from storage.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Gate::authorize('file-manager.delete');

        $validated = $request->validate([
            'disk' => ['required', 'in:public,private'],
            'path' => ['nullable', 'string', 'max:500'],
            'target_path' => ['required', 'string', 'max:500'],
        ]);

        $visibility = $validated['disk'];
        $currentPath = (string) ($validated['path'] ?? '');
        $targetPath = $validated['target_path'];

        try {
            $deleted = $this->fileManagerService->deleteItem($visibility, $targetPath);

            if ($deleted) {
                ActivityLog::record(
                    module: 'file-manager',
                    action: 'deleted',
                    description: "Deleted {$targetPath} from {$visibility} storage",
                    subjectLabel: $targetPath,
                    before: ['disk' => $visibility, 'path' => $targetPath],
                    after: null,
                    subjectId: $targetPath,
                );

                return redirect()
                    ->route('file-manager.index', ['disk' => $visibility, 'path' => $currentPath])
                    ->with('success', __('Item deleted successfully.'));
            }

            return redirect()
                ->route('file-manager.index', ['disk' => $visibility, 'path' => $currentPath])
                ->with('error', __('Target file or folder could not be found.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('file-manager.index', ['disk' => $visibility, 'path' => $currentPath])
                ->with('error', $e->getMessage());
        }
    }
}
