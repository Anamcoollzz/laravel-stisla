<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
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
        return view('modules.teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        Teacher::query()->create(
            $request->safe()->only(['nip', 'name', 'email', 'phone', 'gender', 'address'])
        );

        return redirect()
            ->route('teachers.index')
            ->with('success', __('Teacher created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher): View
    {
        return view('modules.teachers.show', [
            'teacher' => $teacher,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher): View
    {
        return view('modules.teachers.edit', [
            'teacher' => $teacher,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeacherRequest $request, Teacher $teacher): RedirectResponse
    {
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
        $teacher->delete();

        return redirect()
            ->route('teachers.index')
            ->with('success', __('Teacher deleted successfully.'));
    }
}
