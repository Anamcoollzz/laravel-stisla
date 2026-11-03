<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

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
}
