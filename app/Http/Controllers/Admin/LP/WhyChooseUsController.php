<?php

namespace App\Http\Controllers\Admin\LP;

use App\Exports\WhyChooseUsExport;
use App\Exports\WhyChooseUsTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\WhyChooseUsImport;
use App\Models\LP\WhyChooseUs;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WhyChooseUsController extends Controller
{
    /**
     * Display a listing of Why Choose Us items.
     */
    public function index(): View
    {
        Gate::authorize('lp-why-choose-us.view');

        $items = WhyChooseUs::query()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('modules.lp-why-choose-us.index', compact('items'));
    }

    /**
     * Show the form for creating a new Why Choose Us item.
     */
    public function create(): View
    {
        Gate::authorize('lp-why-choose-us.create');

        return view('modules.lp-why-choose-us.create');
    }

    /**
     * Store a newly created Why Choose Us item in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('lp-why-choose-us.create');

        $validated = $this->validateItem($request);

        WhyChooseUs::query()->create($validated);

        return redirect()
            ->route('lp-why-choose-us.index')
            ->with('success', __('Keunggulan (Why Choose Us) berhasil ditambahkan.'));
    }

    /**
     * Display the specified Why Choose Us item.
     */
    public function show(WhyChooseUs $whyChooseUs): View
    {
        Gate::authorize('lp-why-choose-us.view');

        return view('modules.lp-why-choose-us.show', compact('whyChooseUs'));
    }

    /**
     * Show the form for editing the specified Why Choose Us item.
     */
    public function edit(WhyChooseUs $whyChooseUs): View
    {
        Gate::authorize('lp-why-choose-us.edit');

        return view('modules.lp-why-choose-us.edit', compact('whyChooseUs'));
    }

    /**
     * Update the specified Why Choose Us item in storage.
     */
    public function update(Request $request, WhyChooseUs $whyChooseUs): RedirectResponse
    {
        Gate::authorize('lp-why-choose-us.edit');

        $validated = $this->validateItem($request);

        $whyChooseUs->update($validated);

        return redirect()
            ->route('lp-why-choose-us.index')
            ->with('success', __('Keunggulan (Why Choose Us) berhasil diperbarui.'));
    }

    /**
     * Duplicate the specified Why Choose Us item.
     */
    public function duplicate(WhyChooseUs $whyChooseUs): RedirectResponse
    {
        Gate::authorize('lp-why-choose-us.create');

        $copy = $whyChooseUs->replicate();
        $copy->title = Str::limit($whyChooseUs->title, 143, '').' (Copy)';
        $copy->order = ((int) WhyChooseUs::query()->max('order')) + 1;
        $copy->save();

        return redirect()
            ->route('lp-why-choose-us.index')
            ->with('success', __('Keunggulan (Why Choose Us) berhasil diduplikasi.'));
    }

    /**
     * Remove the specified Why Choose Us item from storage.
     */
    public function destroy(WhyChooseUs $whyChooseUs): RedirectResponse
    {
        Gate::authorize('lp-why-choose-us.delete');

        $whyChooseUs->delete();

        return redirect()
            ->route('lp-why-choose-us.index')
            ->with('success', __('Keunggulan (Why Choose Us) berhasil dihapus.'));
    }

    /**
     * Export Why Choose Us items to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('lp-why-choose-us.view');

        return Excel::download(new WhyChooseUsExport, 'lp-why-choose-us-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * Export Why Choose Us items to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('lp-why-choose-us.view');

        $items = WhyChooseUs::query()->orderBy('order')->orderBy('id')->get();
        $pdf = Pdf::loadView('modules.lp-why-choose-us.pdf', compact('items'));

        return $pdf->download('lp-why-choose-us-'.now()->format('Y-m-d-His').'.pdf');
    }

    /**
     * Download sample Excel template for importing Why Choose Us items.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('lp-why-choose-us.create');

        return Excel::download(new WhyChooseUsTemplateExport, 'lp-why-choose-us-template.xlsx');
    }

    /**
     * Import Why Choose Us items from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('lp-why-choose-us.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new WhyChooseUsImport, $request->file('file'));

        return redirect()
            ->route('lp-why-choose-us.index')
            ->with('success', __('Keunggulan (Why Choose Us) berhasil diimport.'));
    }

    /**
     * Validate request payload for Why Choose Us items.
     *
     * @return array<string, mixed>
     */
    private function validateItem(Request $request): array
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
