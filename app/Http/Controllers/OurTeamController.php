<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOurTeamRequest;
use App\Http\Requests\UpdateOurTeamRequest;
use App\Models\OurTeam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OurTeamController extends Controller
{
    /**
     * Display a listing of our team members.
     */
    public function index(): View
    {
        Gate::authorize('our-teams.view');

        $teams = OurTeam::query()->orderBy('order')->latest('id')->get();

        $stats = [
            'total' => $teams->count(),
            'active' => $teams->where('is_active', true)->count(),
            'inactive' => $teams->where('is_active', false)->count(),
        ];

        return view('modules.our-teams.index', compact('teams', 'stats'));
    }

    /**
     * Show the form for creating a new team member.
     */
    public function create(): View
    {
        Gate::authorize('our-teams.create');

        return view('modules.our-teams.create');
    }

    /**
     * Store a newly created team member in storage.
     */
    public function store(StoreOurTeamRequest $request): RedirectResponse
    {
        Gate::authorize('our-teams.create');

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('our-teams', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $data['order'] ?? ((OurTeam::max('order') ?? 0) + 1);

        OurTeam::create($data);

        return redirect()->route('our-teams.index')->with('success', __('Team member created successfully.'));
    }

    /**
     * Display the specified team member.
     */
    public function show(OurTeam $ourTeam): View
    {
        Gate::authorize('our-teams.view');

        return view('modules.our-teams.show', ['team' => $ourTeam]);
    }

    /**
     * Show the form for editing the specified team member.
     */
    public function edit(OurTeam $ourTeam): View
    {
        Gate::authorize('our-teams.edit');

        return view('modules.our-teams.edit', ['team' => $ourTeam]);
    }

    /**
     * Update the specified team member in storage.
     */
    public function update(UpdateOurTeamRequest $request, OurTeam $ourTeam): RedirectResponse
    {
        Gate::authorize('our-teams.edit');

        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($ourTeam->image && Storage::disk('public')->exists($ourTeam->image)) {
                Storage::disk('public')->delete($ourTeam->image);
            }
            $data['image'] = $request->file('image')->store('our-teams', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $ourTeam->update($data);

        return redirect()->route('our-teams.index')->with('success', __('Team member updated successfully.'));
    }

    /**
     * Remove the specified team member from storage.
     */
    public function destroy(OurTeam $ourTeam): RedirectResponse
    {
        Gate::authorize('our-teams.delete');

        if ($ourTeam->image && Storage::disk('public')->exists($ourTeam->image)) {
            Storage::disk('public')->delete($ourTeam->image);
        }

        $ourTeam->delete();

        return redirect()->route('our-teams.index')->with('success', __('Team member deleted successfully.'));
    }

    /**
     * Duplicate the specified team member.
     */
    public function duplicate(OurTeam $ourTeam): RedirectResponse
    {
        Gate::authorize('our-teams.create');

        $clone = $ourTeam->replicate();
        $clone->name = $clone->name.' (Copy)';
        $clone->order = ((int) OurTeam::max('order')) + 1;
        $clone->save();

        return redirect()->route('our-teams.index')->with('success', __('Team member duplicated successfully.'));
    }
}
