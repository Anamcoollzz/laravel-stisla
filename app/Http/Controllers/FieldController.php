<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFieldRequest;
use App\Http\Requests\UpdateFieldRequest;
use App\Models\Field;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class FieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('fields.view');

        $fields = Field::query()->latest()->get();

        return view('modules.fields.index', [
            'fields' => $fields,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('fields.create');

        return view('modules.fields.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFieldRequest $request): RedirectResponse
    {
        Gate::authorize('fields.create');

        Field::query()->create(
            $request->safe()->only(['code', 'name', 'description'])
        );

        return redirect()
            ->route('fields.index')
            ->with('success', __('Field created successfully.'));
    }

    /**
     * Duplicate the specified resource.
     */
    public function duplicate(Field $field): RedirectResponse
    {
        Gate::authorize('fields.create');

        $baseCode = substr($field->code, 0, 20);
        $newCode = $baseCode.'-COPY';
        $counter = 1;
        while (Field::where('code', $newCode)->exists()) {
            $counter++;
            $newCode = substr($field->code, 0, 18).'-CP'.$counter;
        }

        Field::create([
            'code' => $newCode,
            'name' => $field->name.' (Copy)',
            'description' => $field->description,
        ]);

        return redirect()
            ->route('fields.index')
            ->with('success', __('Field duplicated successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Field $field): View
    {
        Gate::authorize('fields.view');

        return view('modules.fields.show', [
            'field' => $field,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Field $field): View
    {
        Gate::authorize('fields.edit');

        return view('modules.fields.edit', [
            'field' => $field,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFieldRequest $request, Field $field): RedirectResponse
    {
        Gate::authorize('fields.edit');

        $field->update(
            $request->safe()->only(['code', 'name', 'description'])
        );

        return redirect()
            ->route('fields.index')
            ->with('success', __('Field updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Field $field): RedirectResponse
    {
        Gate::authorize('fields.delete');

        $field->delete();

        return redirect()
            ->route('fields.index')
            ->with('success', __('Field deleted successfully.'));
    }

    /**
     * Export fields to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('fields.view');

        $fields = Field::query()->latest('id')->get();

        return Pdf::loadView('modules.fields.pdf', [
            'fields' => $fields,
        ])->setPaper('a4', 'portrait')->download('fields_'.date('Y-m-d_His').'.pdf');
    }
}
