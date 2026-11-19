<?php

namespace App\Http\Controllers\Admin\LP;

use App\Exports\CompanyValuesExport;
use App\Exports\CompanyValuesTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\CompanyValuesImport;
use App\Models\LP\CompanyValue;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CompanyValueController extends Controller
{
    /**
     * Display a listing of company values.
     */
    public function index(): View
    {
        Gate::authorize('lp-company-values.view');

        $values = CompanyValue::query()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('modules.lp-company-values.index', compact('values'));
    }

    /**
     * Show the form for creating a new company value.
     */
    public function create(): View
    {
        Gate::authorize('lp-company-values.create');

        return view('modules.lp-company-values.create');
    }

    /**
     * Store a newly created company value in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('lp-company-values.create');

        $validated = $this->validateValue($request);

        CompanyValue::query()->create($validated);

        return redirect()
            ->route('lp-company-values.index')
            ->with('success', __('Nilai perusahaan berhasil ditambahkan.'));
    }

    /**
     * Display the specified company value.
     */
    public function show(CompanyValue $companyValue): View
    {
        Gate::authorize('lp-company-values.view');

        return view('modules.lp-company-values.show', compact('companyValue'));
    }

    /**
     * Show the form for editing the specified company value.
     */
    public function edit(CompanyValue $companyValue): View
    {
        Gate::authorize('lp-company-values.edit');

        return view('modules.lp-company-values.edit', compact('companyValue'));
    }

    /**
     * Update the specified company value in storage.
     */
    public function update(Request $request, CompanyValue $companyValue): RedirectResponse
    {
        Gate::authorize('lp-company-values.edit');

        $validated = $this->validateValue($request);

        $companyValue->update($validated);

        return redirect()
            ->route('lp-company-values.index')
            ->with('success', __('Nilai perusahaan berhasil diperbarui.'));
    }

    /**
     * Duplicate the specified company value.
     */
    public function duplicate(CompanyValue $companyValue): RedirectResponse
    {
        Gate::authorize('lp-company-values.create');

        $copy = $companyValue->replicate();
        $copy->title = Str::limit($companyValue->title, 143, '').' (Copy)';
        $copy->order = ((int) CompanyValue::query()->max('order')) + 1;
        $copy->save();

        return redirect()
            ->route('lp-company-values.index')
            ->with('success', __('Nilai perusahaan berhasil diduplikasi.'));
    }

    /**
     * Remove the specified company value from storage.
     */
    public function destroy(CompanyValue $companyValue): RedirectResponse
    {
        Gate::authorize('lp-company-values.delete');

        $companyValue->delete();

        return redirect()
            ->route('lp-company-values.index')
            ->with('success', __('Nilai perusahaan berhasil dihapus.'));
    }

    /**
     * Export company values to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('lp-company-values.view');

        return Excel::download(new CompanyValuesExport, 'lp-company-values-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * Export company values to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('lp-company-values.view');

        $values = CompanyValue::query()->orderBy('order')->orderBy('id')->get();
        $pdf = Pdf::loadView('modules.lp-company-values.pdf', compact('values'));

        return $pdf->download('lp-company-values-'.now()->format('Y-m-d-His').'.pdf');
    }

    /**
     * Download sample Excel template for importing company values.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('lp-company-values.create');

        return Excel::download(new CompanyValuesTemplateExport, 'lp-company-values-template.xlsx');
    }

    /**
     * Import company values from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('lp-company-values.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new CompanyValuesImport, $request->file('file'));

        return redirect()
            ->route('lp-company-values.index')
            ->with('success', __('Nilai perusahaan berhasil diimport.'));
    }

    /**
     * Validate request payload for company values.
     *
     * @return array<string, mixed>
     */
    private function validateValue(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'icon' => ['required', 'string', 'max:100'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['order'] = (int) ($validated['order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
