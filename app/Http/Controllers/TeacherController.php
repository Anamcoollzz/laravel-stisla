<?php

namespace App\Http\Controllers;

use App\Exports\TeachersExport;
use App\Exports\TeachersTemplateExport;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Imports\TeachersImport;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('teachers.view');

        $teachers = Teacher::query()->latest()->get();

        return view('modules.teachers.index', [
            'teachers' => $teachers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('teachers.create');

        return view('modules.teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        Gate::authorize('teachers.create');

        Teacher::query()->create(
            $request->safe()->only(['nip', 'name', 'email', 'phone', 'gender', 'address'])
        );

        return redirect()
            ->route('teachers.index')
            ->with('success', __('Teacher created successfully.'));
    }

    /**
     * Duplicate the specified resource.
     */
    public function duplicate(Teacher $teacher): RedirectResponse
    {
        Gate::authorize('teachers.create');

        $baseNip = substr($teacher->nip, 0, 20);
        $newNip = $baseNip.'-'.rand(100, 999);
        while (Teacher::where('nip', $newNip)->exists()) {
            $newNip = $baseNip.'-'.rand(1000, 9999);
        }

        $emailParts = explode('@', $teacher->email);
        $newEmail = $emailParts[0].'+copy'.rand(100, 999).'@'.($emailParts[1] ?? 'example.com');
        while (Teacher::where('email', $newEmail)->exists()) {
            $newEmail = $emailParts[0].'+copy'.rand(1000, 99999).'@'.($emailParts[1] ?? 'example.com');
        }

        Teacher::create([
            'nip' => $newNip,
            'name' => $teacher->name.' (Copy)',
            'email' => $newEmail,
            'phone' => $teacher->phone,
            'gender' => $teacher->gender,
            'address' => $teacher->address,
        ]);

        return redirect()
            ->route('teachers.index')
            ->with('success', __('Teacher duplicated successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher): View
    {
        Gate::authorize('teachers.view');

        return view('modules.teachers.show', [
            'teacher' => $teacher,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher): View
    {
        Gate::authorize('teachers.edit');

        return view('modules.teachers.edit', [
            'teacher' => $teacher,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        Gate::authorize('teachers.edit');

        $teacher->update(
            $request->safe()->only(['nip', 'name', 'email', 'phone', 'gender', 'address'])
        );

        return redirect()
            ->route('teachers.index')
            ->with('success', __('Teacher updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher): RedirectResponse
    {
        Gate::authorize('teachers.delete');

        $teacher->delete();

        return redirect()
            ->route('teachers.index')
            ->with('success', __('Teacher deleted successfully.'));
    }

    /**
     * Export teachers to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('teachers.view');

        return Excel::download(new TeachersExport, 'teachers_'.date('Y-m-d_His').'.xlsx');
    }

    /**
     * Download teacher import template.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('teachers.create');

        return Excel::download(new TeachersTemplateExport, 'teachers_import_example.xlsx');
    }

    /**
     * Import teachers from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('teachers.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            Excel::import(new TeachersImport, $request->file('file'));

            return redirect()
                ->route('teachers.index')
                ->with('success', __('Data imported successfully.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('teachers.index')
                ->with('error', __('Import failed: ').$e->getMessage());
        }
    }
}
