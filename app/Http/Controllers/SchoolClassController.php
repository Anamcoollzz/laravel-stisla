<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Models\SchoolClass;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $classes = SchoolClass::query()->latest()->get();

        return view('modules.classes.index', [
            'classes' => $classes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('modules.classes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSchoolClassRequest $request): RedirectResponse
    {
        SchoolClass::query()->create($request->safe()->only(['class_name']));

        return redirect()
            ->route('classes.index')
            ->with('success', __('Class created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(SchoolClass $schoolClass): View
    {
        return view('modules.classes.show', [
            'schoolClass' => $schoolClass,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SchoolClass $schoolClass): View
    {
        return view('modules.classes.edit', [
            'schoolClass' => $schoolClass,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSchoolClassRequest $request, SchoolClass $schoolClass): RedirectResponse
    {
        $schoolClass->update($request->safe()->only(['class_name']));

        return redirect()
            ->route('classes.index')
            ->with('success', __('Class updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolClass $schoolClass): RedirectResponse
    {
        $schoolClass->delete();

        return redirect()
            ->route('classes.index')
            ->with('success', __('Class deleted successfully.'));
    }
}
