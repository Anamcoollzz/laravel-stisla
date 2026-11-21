<?php

namespace App\Http\Controllers\Admin\LP;

use App\Exports\RecruitmentStepsExport;
use App\Exports\RecruitmentStepsTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\RecruitmentStepsImport;
use App\Models\LP\RecruitmentStep;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RecruitmentStepController extends Controller
{
    /**
     * Display a listing of recruitment steps.
     */
    public function index(): View
    {
        Gate::authorize('lp-recruitment-steps.view');

        $steps = RecruitmentStep::query()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('modules.lp-recruitment-steps.index', compact('steps'));
    }

    /**
     * Show the form for creating a new recruitment step.
     */
    public function create(): View
    {
        Gate::authorize('lp-recruitment-steps.create');

        return view('modules.lp-recruitment-steps.create');
    }

    /**
     * Store a newly created recruitment step in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('lp-recruitment-steps.create');

        $validated = $this->validateStep($request);

        RecruitmentStep::query()->create($validated);

        return redirect()
            ->route('lp-recruitment-steps.index')
            ->with('success', __('Tahapan rekrutmen berhasil ditambahkan.'));
    }

    /**
     * Display the specified recruitment step.
     */
    public function show(RecruitmentStep $recruitmentStep): View
    {
        Gate::authorize('lp-recruitment-steps.view');

        return view('modules.lp-recruitment-steps.show', compact('recruitmentStep'));
    }

    /**
     * Show the form for editing the specified recruitment step.
     */
    public function edit(RecruitmentStep $recruitmentStep): View
    {
        Gate::authorize('lp-recruitment-steps.edit');

        return view('modules.lp-recruitment-steps.edit', compact('recruitmentStep'));
    }

    /**
     * Update the specified recruitment step in storage.
     */
    public function update(Request $request, RecruitmentStep $recruitmentStep): RedirectResponse
    {
        Gate::authorize('lp-recruitment-steps.edit');

        $validated = $this->validateStep($request);

        $recruitmentStep->update($validated);

        return redirect()
            ->route('lp-recruitment-steps.index')
            ->with('success', __('Tahapan rekrutmen berhasil diperbarui.'));
    }

    /**
     * Duplicate the specified recruitment step.
     */
    public function duplicate(RecruitmentStep $recruitmentStep): RedirectResponse
    {
        Gate::authorize('lp-recruitment-steps.create');

        $copy = $recruitmentStep->replicate();
        $copy->title = Str::limit($recruitmentStep->title, 143, '').' (Copy)';
        $copy->order = ((int) RecruitmentStep::query()->max('order')) + 1;
        $copy->save();

        return redirect()
            ->route('lp-recruitment-steps.index')
            ->with('success', __('Tahapan rekrutmen berhasil diduplikasi.'));
    }

    /**
     * Remove the specified recruitment step from storage.
     */
    public function destroy(RecruitmentStep $recruitmentStep): RedirectResponse
    {
        Gate::authorize('lp-recruitment-steps.delete');

        $recruitmentStep->delete();

        return redirect()
            ->route('lp-recruitment-steps.index')
            ->with('success', __('Tahapan rekrutmen berhasil dihapus.'));
    }

    /**
     * Export recruitment steps to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('lp-recruitment-steps.view');

        return Excel::download(new RecruitmentStepsExport, 'lp-recruitment-steps-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * Export recruitment steps to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('lp-recruitment-steps.view');

        $steps = RecruitmentStep::query()->orderBy('order')->orderBy('id')->get();
        $pdf = Pdf::loadView('modules.lp-recruitment-steps.pdf', compact('steps'));

        return $pdf->download('lp-recruitment-steps-'.now()->format('Y-m-d-His').'.pdf');
    }

    /**
     * Download sample Excel template for importing recruitment steps.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('lp-recruitment-steps.create');

        return Excel::download(new RecruitmentStepsTemplateExport, 'lp-recruitment-steps-template.xlsx');
    }

    /**
     * Import recruitment steps from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('lp-recruitment-steps.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new RecruitmentStepsImport, $request->file('file'));

        return redirect()
            ->route('lp-recruitment-steps.index')
            ->with('success', __('Tahapan rekrutmen berhasil diimport.'));
    }

    /**
     * Validate request payload for recruitment steps.
     *
     * @return array<string, mixed>
     */
    private function validateStep(Request $request): array
    {
        $validated = $request->validate([
            'step' => ['required', 'string', 'max:20'],
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
