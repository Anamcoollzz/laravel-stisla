<?php

namespace App\Http\Controllers\Admin\LP;

use App\Exports\CompanyStatsExport;
use App\Exports\CompanyStatsTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\CompanyStatsImport;
use App\Models\LP\CompanyStat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CompanyStatController extends Controller
{
    /**
     * Display a listing of company statistics.
     */
    public function index(): View
    {
        Gate::authorize('lp-company-stats.view');

        $stats = CompanyStat::query()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('modules.lp-company-stats.index', compact('stats'));
    }

    /**
     * Show the form for creating a new company statistic.
     */
    public function create(): View
    {
        Gate::authorize('lp-company-stats.create');

        return view('modules.lp-company-stats.create');
    }

    /**
     * Store a newly created company statistic in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('lp-company-stats.create');

        $validated = $this->validateStat($request);

        CompanyStat::query()->create($validated);

        return redirect()
            ->route('lp-company-stats.index')
            ->with('success', __('Statistik perusahaan berhasil ditambahkan.'));
    }

    /**
     * Display the specified company statistic.
     */
    public function show(CompanyStat $companyStat): View
    {
        Gate::authorize('lp-company-stats.view');

        return view('modules.lp-company-stats.show', compact('companyStat'));
    }

    /**
     * Show the form for editing the specified company statistic.
     */
    public function edit(CompanyStat $companyStat): View
    {
        Gate::authorize('lp-company-stats.edit');

        return view('modules.lp-company-stats.edit', compact('companyStat'));
    }

    /**
     * Update the specified company statistic in storage.
     */
    public function update(Request $request, CompanyStat $companyStat): RedirectResponse
    {
        Gate::authorize('lp-company-stats.edit');

        $validated = $this->validateStat($request);

        $companyStat->update($validated);

        return redirect()
            ->route('lp-company-stats.index')
            ->with('success', __('Statistik perusahaan berhasil diperbarui.'));
    }

    /**
     * Remove the specified company statistic from storage.
     */
    public function destroy(CompanyStat $companyStat): RedirectResponse
    {
        Gate::authorize('lp-company-stats.delete');

        $companyStat->delete();

        return redirect()
            ->route('lp-company-stats.index')
            ->with('success', __('Statistik perusahaan berhasil dihapus.'));
    }

    /**
     * Export company statistics to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('lp-company-stats.view');

        return Excel::download(new CompanyStatsExport, 'company-stats-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * Export company statistics to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('lp-company-stats.view');

        $stats = CompanyStat::query()->orderBy('order')->orderBy('id')->get();
        $pdf = Pdf::loadView('modules.lp-company-stats.pdf', compact('stats'));

        return $pdf->download('company-stats-'.now()->format('Y-m-d-His').'.pdf');
    }

    /**
     * Download sample Excel template for importing company statistics.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('lp-company-stats.create');

        return Excel::download(new CompanyStatsTemplateExport, 'company-stats-template.xlsx');
    }

    /**
     * Import company statistics from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('lp-company-stats.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new CompanyStatsImport, $request->file('file'));

        return redirect()
            ->route('lp-company-stats.index')
            ->with('success', __('Statistik perusahaan berhasil diimport.'));
    }

    /**
     * Validate request payload for company statistics.
     *
     * @return array<string, mixed>
     */
    private function validateStat(Request $request): array
    {
        $validated = $request->validate([
            'value' => ['required', 'string', 'max:50'],
            'label' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'icon' => ['required', 'string', 'max:100'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['order'] = (int) ($validated['order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
