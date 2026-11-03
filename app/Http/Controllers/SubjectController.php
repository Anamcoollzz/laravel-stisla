<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('subjects.view');

        $subjects = Subject::query()->latest()->get();

        return view('modules.subjects.index', [
            'subjects' => $subjects,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('subjects.create');

        return view('modules.subjects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        Gate::authorize('subjects.create');

        Subject::query()->create(
            $request->safe()->only(['code', 'name', 'description'])
        );

        return redirect()
            ->route('subjects.index')
            ->with('success', __('Subject created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject): View
    {
        Gate::authorize('subjects.view');

        return view('modules.subjects.show', [
            'subject' => $subject,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject): View
    {
        Gate::authorize('subjects.edit');

        return view('modules.subjects.edit', [
            'subject' => $subject,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        Gate::authorize('subjects.edit');

        $subject->update(
            $request->safe()->only(['code', 'name', 'description'])
        );

        return redirect()
            ->route('subjects.index')
            ->with('success', __('Subject updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject): RedirectResponse
    {
        Gate::authorize('subjects.delete');

        $subject->delete();

        return redirect()
            ->route('subjects.index')
            ->with('success', __('Subject deleted successfully.'));
    }
}
