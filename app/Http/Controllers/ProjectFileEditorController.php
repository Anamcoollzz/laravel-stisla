<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\ProjectFileEditorService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectFileEditorController extends Controller
{
    public function __construct(
        protected ProjectFileEditorService $editorService
    ) {}

    /**
     * Browse project root and nested directories.
     */
    public function index(Request $request): View
    {
        Gate::authorize('project-files.view');

        $path = $request->string('path')->toString();
        $search = $request->string('search')->toString();

        try {
            $listing = $this->editorService->listDirectory($path, $search);
        } catch (\InvalidArgumentException $e) {
            $listing = $this->editorService->listDirectory('', $search);
            session()->flash('error', $e->getMessage());
        }

        return view('modules.project-files.index', [
            ...$listing,
            'search' => $search,
        ]);
    }

    /**
     * Open a project file in the code/text editor.
     */
    public function edit(Request $request): View|RedirectResponse
    {
        Gate::authorize('project-files.edit');

        $filePath = $request->string('file')->toString();

        try {
            $file = $this->editorService->readFile($filePath);

            return view('modules.project-files.edit', $file);
        } catch (\Throwable $e) {
            return redirect()
                ->route('project-files.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Save edited file content back to disk.
     */
    public function update(Request $request): RedirectResponse
    {
        Gate::authorize('project-files.edit');

        $validated = $request->validate([
            'file' => ['required', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
        ]);

        $filePath = $validated['file'];

        try {
            $sizes = $this->editorService->updateFile($filePath, (string) ($validated['content'] ?? ''));

            ActivityLog::record(
                module: 'project-files',
                action: 'updated',
                description: "Edited project file {$filePath}",
                subjectLabel: $filePath,
                before: ['size' => $sizes['before_size']],
                after: ['size' => $sizes['after_size']],
                subjectId: $filePath,
            );

            return redirect()
                ->route('project-files.edit', ['file' => $filePath])
                ->with('success', __('File saved successfully.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('project-files.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create a new file inside the current directory.
     */
    public function storeFile(Request $request): RedirectResponse
    {
        Gate::authorize('project-files.create');

        $validated = $request->validate([
            'path' => ['nullable', 'string', 'max:1000'],
            'name' => ['required', 'string', 'max:180'],
            'content' => ['nullable', 'string'],
        ]);

        $path = (string) ($validated['path'] ?? '');

        try {
            $createdPath = $this->editorService->createFile(
                $path,
                $validated['name'],
                (string) ($validated['content'] ?? '')
            );

            ActivityLog::record(
                module: 'project-files',
                action: 'created_file',
                description: "Created project file {$createdPath}",
                subjectLabel: $createdPath,
                before: null,
                after: ['path' => $createdPath],
                subjectId: $createdPath,
            );

            return redirect()
                ->route('project-files.edit', ['file' => $createdPath])
                ->with('success', __('File created successfully.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('project-files.index', ['path' => $path])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create a new folder inside the current directory.
     */
    public function storeFolder(Request $request): RedirectResponse
    {
        Gate::authorize('project-files.create');

        $validated = $request->validate([
            'path' => ['nullable', 'string', 'max:1000'],
            'name' => ['required', 'string', 'max:180'],
        ]);

        $path = (string) ($validated['path'] ?? '');

        try {
            $createdPath = $this->editorService->createFolder($path, $validated['name']);

            ActivityLog::record(
                module: 'project-files',
                action: 'created_folder',
                description: "Created project folder {$createdPath}",
                subjectLabel: $createdPath,
                before: null,
                after: ['path' => $createdPath],
                subjectId: $createdPath,
            );

            return redirect()
                ->route('project-files.index', ['path' => $path])
                ->with('success', __('Folder created successfully.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('project-files.index', ['path' => $path])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Rename a project file or folder.
     */
    public function rename(Request $request): RedirectResponse
    {
        Gate::authorize('project-files.edit');

        $validated = $request->validate([
            'path' => ['nullable', 'string', 'max:1000'],
            'target_path' => ['required', 'string', 'max:1000'],
            'new_name' => ['required', 'string', 'max:180'],
        ]);

        $currentPath = (string) ($validated['path'] ?? '');

        try {
            $newPath = $this->editorService->renameItem($validated['target_path'], $validated['new_name']);

            ActivityLog::record(
                module: 'project-files',
                action: 'renamed',
                description: "Renamed project item {$validated['target_path']} to {$newPath}",
                subjectLabel: $newPath,
                before: ['path' => $validated['target_path']],
                after: ['path' => $newPath],
                subjectId: $newPath,
            );

            return redirect()
                ->route('project-files.index', ['path' => $currentPath])
                ->with('success', __('Item renamed successfully.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('project-files.index', ['path' => $currentPath])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete a project file or folder.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Gate::authorize('project-files.delete');

        $validated = $request->validate([
            'path' => ['nullable', 'string', 'max:1000'],
            'target_path' => ['required', 'string', 'max:1000'],
        ]);

        $currentPath = (string) ($validated['path'] ?? '');
        $targetPath = $validated['target_path'];

        try {
            $deleted = $this->editorService->deleteItem($targetPath);

            if ($deleted) {
                ActivityLog::record(
                    module: 'project-files',
                    action: 'deleted',
                    description: "Deleted project item {$targetPath}",
                    subjectLabel: $targetPath,
                    before: ['path' => $targetPath],
                    after: null,
                    subjectId: $targetPath,
                );

                return redirect()
                    ->route('project-files.index', ['path' => $currentPath])
                    ->with('success', __('Item deleted successfully.'));
            }

            return redirect()
                ->route('project-files.index', ['path' => $currentPath])
                ->with('error', __('Target file or folder could not be found.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('project-files.index', ['path' => $currentPath])
                ->with('error', $e->getMessage());
        }
    }
}
