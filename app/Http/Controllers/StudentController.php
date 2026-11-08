<?php

namespace App\Http\Controllers;

use App\Exports\StudentsExport;
use App\Exports\StudentsTemplateExport;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Imports\StudentsImport;
use App\Models\SchoolClass;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('students.view');

        $students = Student::query()
            ->with('schoolClass')
            ->latest()
            ->get();

        return view('modules.students.index', [
            'students' => $students,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('students.create');

        $classes = SchoolClass::query()->orderBy('class_name')->get();

        return view('modules.students.create', [
            'classes' => $classes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        Gate::authorize('students.create');

        Student::query()->create(
            $request->safe()->only(['class_id', 'nis', 'name', 'email', 'phone', 'gender', 'birth_date', 'address'])
        );

        return redirect()
            ->route('students.index')
            ->with('success', __('Student created successfully.'));
    }

    /**
     * Duplicate the specified resource.
     */
    public function duplicate(Student $student): RedirectResponse
    {
        Gate::authorize('students.create');

        $baseNis = substr($student->nis, 0, 20);
        $newNis = $baseNis.'-'.rand(100, 999);
        while (Student::where('nis', $newNis)->exists()) {
            $newNis = $baseNis.'-'.rand(1000, 9999);
        }

        $newEmail = null;
        if ($student->email) {
            $emailParts = explode('@', $student->email);
            $newEmail = $emailParts[0].'+copy'.rand(100, 999).'@'.($emailParts[1] ?? 'example.com');
            while (Student::where('email', $newEmail)->exists()) {
                $newEmail = $emailParts[0].'+copy'.rand(1000, 99999).'@'.($emailParts[1] ?? 'example.com');
            }
        }

        Student::create([
            'class_id' => $student->class_id,
            'nis' => $newNis,
            'name' => $student->name.' (Copy)',
            'email' => $newEmail,
            'phone' => $student->phone,
            'gender' => $student->gender,
            'birth_date' => $student->birth_date,
            'address' => $student->address,
        ]);

        return redirect()
            ->route('students.index')
            ->with('success', __('Student duplicated successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student): View
    {
        Gate::authorize('students.view');

        $student->load('schoolClass');

        return view('modules.students.show', [
            'student' => $student,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student): View
    {
        Gate::authorize('students.edit');

        $classes = SchoolClass::query()->orderBy('class_name')->get();

        return view('modules.students.edit', [
            'student' => $student,
            'classes' => $classes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        Gate::authorize('students.edit');

        $student->update(
            $request->safe()->only(['class_id', 'nis', 'name', 'email', 'phone', 'gender', 'birth_date', 'address'])
        );

        return redirect()
            ->route('students.index')
            ->with('success', __('Student updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student): RedirectResponse
    {
        Gate::authorize('students.delete');

        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('success', __('Student deleted successfully.'));
    }

    /**
     * Export students to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('students.view');

        return Excel::download(new StudentsExport, 'students_'.date('Y-m-d_His').'.xlsx');
    }

    /**
     * Export students to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('students.view');

        $students = Student::query()->with('schoolClass')->latest('id')->get();

        return Pdf::loadView('modules.students.pdf', [
            'students' => $students,
        ])->setPaper('a4', 'landscape')->download('students_'.date('Y-m-d_His').'.pdf');
    }

    /**
     * Download student import template.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('students.create');

        return Excel::download(new StudentsTemplateExport, 'students_import_example.xlsx');
    }

    /**
     * Import students from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('students.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));

            return redirect()
                ->route('students.index')
                ->with('success', __('Data imported successfully.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('students.index')
                ->with('error', __('Import failed: ').$e->getMessage());
        }
    }
}
