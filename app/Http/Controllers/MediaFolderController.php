<?php

namespace App\Http\Controllers;

use App\Models\MediaFolder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MediaFolderController extends Controller
{
    /**
     * Store a newly created media folder.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        Gate::authorize('media.create');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'integer', Rule::exists('media_folders', 'id')],
        ]);

        $folder = MediaFolder::query()->create([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'-'.Str::lower(Str::random(4)),
            'created_by' => $request->user()?->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Folder created successfully.'),
                'folder' => $folder,
            ], 201);
        }

        return back()->with('success', __('Folder created successfully.'));
    }

    /**
     * Rename the specified media folder.
     */
    public function update(Request $request, MediaFolder $folder): RedirectResponse|JsonResponse
    {
        Gate::authorize('media.edit');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $folder->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'-'.$folder->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Folder renamed successfully.'),
                'folder' => $folder,
            ]);
        }

        return back()->with('success', __('Folder renamed successfully.'));
    }

    /**
     * Remove the specified media folder.
     */
    public function destroy(Request $request, MediaFolder $folder): RedirectResponse|JsonResponse
    {
        Gate::authorize('media.delete');

        $folder->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Folder deleted successfully.'),
            ]);
        }

        return redirect()
            ->route('media.index')
            ->with('success', __('Folder deleted successfully.'));
    }
}
