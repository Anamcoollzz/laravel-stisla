<?php

namespace App\Http\Controllers\Admin\LP;

use App\Exports\CompanyMilestonesExport;
use App\Exports\CompanyMilestonesTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\CompanyMilestonesImport;
use App\Models\LP\CompanyMilestone;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CompanyMilestoneController extends Controller
{
    /**
     * Display a listing of company milestones.
     */
    public function index(): View
    {
        Gate::authorize('lp-milestones.view');

        $milestones = CompanyMilestone::query()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('modules.lp-milestones.index', compact('milestones'));
    }

    /**
     * Show the form for creating a new company milestone.
     */
    public function create(): View
    {
        Gate::authorize('lp-milestones.create');

        return view('modules.lp-milestones.create');
    }

    /**
     * Store a newly created company milestone in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('lp-milestones.create');

        $validated = $this->validateMilestone($request);

        CompanyMilestone::query()->create($validated);

        return redirect()
            ->route('lp-milestones.index')
            ->with('success', __('Milestone perusahaan berhasil ditambahkan.'));
    }

    /**
     * Display the specified company milestone.
     */
    public function show(CompanyMilestone $companyMilestone): View
    {
        Gate::authorize('lp-milestones.view');

        return view('modules.lp-milestones.show', compact('companyMilestone'));
    }

    /**
     * Show the form for editing the specified company milestone.
     */
    public function edit(CompanyMilestone $companyMilestone): View
    {
        Gate::authorize('lp-milestones.edit');

        return view('modules.lp-milestones.edit', compact('companyMilestone'));
    }

    /**
     * Update the specified company milestone in storage.
     */
    public function update(Request $request, CompanyMilestone $companyMilestone): RedirectResponse
    {
        Gate::authorize('lp-milestones.edit');

        $validated = $this->validateMilestone($request);

        $companyMilestone->update($validated);

        return redirect()
            ->route('lp-milestones.index')
            ->with('success', __('Milestone perusahaan berhasil diperbarui.'));
    }

    /**
     * Duplicate the specified company milestone.
     */
    public function duplicate(CompanyMilestone $companyMilestone): RedirectResponse
    {
        Gate::authorize('lp-milestones.create');

        $copy = $companyMilestone->replicate();
        $copy->title = Str::limit($companyMilestone->title, 143, '').' (Copy)';
        $copy->order = ((int) CompanyMilestone::query()->max('order')) + 1;
        $copy->save();

        return redirect()
            ->route('lp-milestones.index')
            ->with('success', __('Milestone perusahaan berhasil diduplikasi.'));
    }

    /**
     * Remove the specified company milestone from storage.
     */
    public function destroy(CompanyMilestone $companyMilestone): RedirectResponse
    {
        Gate::authorize('lp-milestones.delete');

        $companyMilestone->delete();

        return redirect()
            ->route('lp-milestones.index')
            ->with('success', __('Milestone perusahaan berhasil dihapus.'));
    }

    /**
     * Export company milestones to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('lp-milestones.view');

        return Excel::download(new CompanyMilestonesExport, 'lp-milestones-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * Export company milestones to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('lp-milestones.view');

        $milestones = CompanyMilestone::query()->orderBy('order')->orderBy('id')->get();
        $pdf = Pdf::loadView('modules.lp-milestones.pdf', compact('milestones'));

        return $pdf->download('lp-milestones-'.now()->format('Y-m-d-His').'.pdf');
    }

    /**
     * Download sample Excel template for importing company milestones.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('lp-milestones.create');

        return Excel::download(new CompanyMilestonesTemplateExport, 'lp-milestones-template.xlsx');
    }

    /**
     * Import company milestones from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('lp-milestones.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new CompanyMilestonesImport, $request->file('file'));

        return redirect()
            ->route('lp-milestones.index')
            ->with('success', __('Milestone perusahaan berhasil diimport.'));
    }

    /**
     * Validate request payload for company milestones.
     *
     * @return array<string, mixed>
     */
    private function validateMilestone(Request $request): array
    {
        $validated = $request->validate([
            'year' => ['required', 'string', 'max:30'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['order'] = (int) ($validated['order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
