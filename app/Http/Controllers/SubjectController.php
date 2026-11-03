<?php

namespace App\Http\Controllers;

use App\Exports\SubjectsExport;
use App\Exports\SubjectsTemplateExport;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Imports\SubjectsImport;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

    /**
     * Export subjects to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('subjects.view');

        return Excel::download(new SubjectsExport, 'subjects_'.date('Y-m-d_His').'.xlsx');
    }

    /**
     * Download subject import template.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('subjects.create');

        return Excel::download(new SubjectsTemplateExport, 'subjects_import_example.xlsx');
    }

    /**
     * Import subjects from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('subjects.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            Excel::import(new SubjectsImport, $request->file('file'));

            return redirect()
                ->route('subjects.index')
                ->with('success', __('Data imported successfully.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('subjects.index')
                ->with('error', __('Import failed: ').$e->getMessage());
        }
    }
}
