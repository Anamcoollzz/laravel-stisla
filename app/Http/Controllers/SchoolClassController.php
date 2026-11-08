<?php

namespace App\Http\Controllers;

use App\Exports\SchoolClassesExport;
use App\Exports\SchoolClassesTemplateExport;
use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Imports\SchoolClassesImport;
use App\Models\SchoolClass;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('classes.view');

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
        Gate::authorize('classes.create');

        return view('modules.classes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSchoolClassRequest $request): RedirectResponse
    {
        Gate::authorize('classes.create');

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
        Gate::authorize('classes.view');

        return view('modules.classes.show', [
            'schoolClass' => $schoolClass,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SchoolClass $schoolClass): View
    {
        Gate::authorize('classes.edit');

        return view('modules.classes.edit', [
            'schoolClass' => $schoolClass,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSchoolClassRequest $request, SchoolClass $schoolClass): RedirectResponse
    {
        Gate::authorize('classes.edit');

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
        Gate::authorize('classes.delete');

        $schoolClass->delete();

        return redirect()
            ->route('classes.index')
            ->with('success', __('Class deleted successfully.'));
    }

    /**
     * Export classes to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('classes.view');

        return Excel::download(new SchoolClassesExport, 'classes_'.date('Y-m-d_His').'.xlsx');
    }

    /**
     * Download class import template.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('classes.create');

        return Excel::download(new SchoolClassesTemplateExport, 'classes_import_example.xlsx');
    }

    /**
     * Import classes from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('classes.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            Excel::import(new SchoolClassesImport, $request->file('file'));

            return redirect()
                ->route('classes.index')
                ->with('success', __('Data imported successfully.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('classes.index')
                ->with('error', __('Import failed: ').$e->getMessage());
        }
    }
}
