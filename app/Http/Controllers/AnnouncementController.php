<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('announcements.view');

        $announcements = Announcement::query()
            ->with('author')
            ->orderByDesc('is_pinned')
            ->latest('id')
            ->get();

        return view('modules.announcements.index', [
            'announcements' => $announcements,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('announcements.create');

        return view('modules.announcements.create', [
            'types' => Announcement::types(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        Gate::authorize('announcements.create');

        $validated = $request->validated();
        $validated['slug'] = Announcement::generateUniqueSlug($validated['title']);
        $validated['created_by'] = $request->user()?->id;

        Announcement::query()->create($validated);

        return redirect()
            ->route('announcements.index')
            ->with('success', __('Announcement created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement): View
    {
        Gate::authorize('announcements.view');

        $announcement->load('author');

        return view('modules.announcements.show', [
            'announcement' => $announcement,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement): View
    {
        Gate::authorize('announcements.edit');

        return view('modules.announcements.edit', [
            'announcement' => $announcement,
            'types' => Announcement::types(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        Gate::authorize('announcements.edit');

        $validated = $request->validated();
        if ($announcement->title !== $validated['title']) {
            $validated['slug'] = Announcement::generateUniqueSlug($validated['title'], $announcement->id);
        }

        $announcement->update($validated);

        return redirect()
            ->route('announcements.index')
            ->with('success', __('Announcement updated successfully.'));
    }

    /**
     * Duplicate the specified resource.
     */
    public function duplicate(Announcement $announcement): RedirectResponse
    {
        Gate::authorize('announcements.create');

        $newTitle = $announcement->title.' (Copy)';

        Announcement::query()->create([
            'title' => $newTitle,
            'slug' => Announcement::generateUniqueSlug($newTitle),
            'content' => $announcement->content,
            'type' => $announcement->type,
            'published_at' => $announcement->published_at,
            'expires_at' => $announcement->expires_at,
            'is_active' => $announcement->is_active,
            'is_pinned' => false,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('announcements.index')
            ->with('success', __('Announcement duplicated successfully.'));
    }

    /**
     * Toggle active status for the specified announcement.
     */
    public function toggleActive(Announcement $announcement): RedirectResponse
    {
        Gate::authorize('announcements.edit');

        $announcement->update([
            'is_active' => ! $announcement->is_active,
        ]);

        return redirect()
            ->route('announcements.index')
            ->with('success', __('Announcement status updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        Gate::authorize('announcements.delete');

        $announcement->delete();

        return redirect()
            ->route('announcements.index')
            ->with('success', __('Announcement deleted successfully.'));
    }
}
