<?php

namespace App\Http\Controllers\Admin\LP;

use App\Exports\LpFaqsExport;
use App\Exports\LpFaqsTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\LpFaqsImport;
use App\Models\LP\LpFaq;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LpFaqController extends Controller
{
    /**
     * Display a listing of LP FAQs.
     */
    public function index(): View
    {
        Gate::authorize('lp-faqs.view');

        $faqs = LpFaq::query()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('modules.lp-faqs.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new LP FAQ.
     */
    public function create(): View
    {
        Gate::authorize('lp-faqs.create');

        return view('modules.lp-faqs.create');
    }

    /**
     * Store a newly created LP FAQ in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('lp-faqs.create');

        $validated = $this->validateFaq($request);

        LpFaq::query()->create($validated);

        return redirect()
            ->route('lp-faqs.index')
            ->with('success', __('FAQ berhasil ditambahkan.'));
    }

    /**
     * Display the specified LP FAQ.
     */
    public function show(LpFaq $lpFaq): View
    {
        Gate::authorize('lp-faqs.view');

        return view('modules.lp-faqs.show', compact('lpFaq'));
    }

    /**
     * Show the form for editing the specified LP FAQ.
     */
    public function edit(LpFaq $lpFaq): View
    {
        Gate::authorize('lp-faqs.edit');

        return view('modules.lp-faqs.edit', compact('lpFaq'));
    }

    /**
     * Update the specified LP FAQ in storage.
     */
    public function update(Request $request, LpFaq $lpFaq): RedirectResponse
    {
        Gate::authorize('lp-faqs.edit');

        $validated = $this->validateFaq($request);

        $lpFaq->update($validated);

        return redirect()
            ->route('lp-faqs.index')
            ->with('success', __('FAQ berhasil diperbarui.'));
    }

    /**
     * Duplicate the specified LP FAQ.
     */
    public function duplicate(LpFaq $lpFaq): RedirectResponse
    {
        Gate::authorize('lp-faqs.create');

        $copy = $lpFaq->replicate();
        $copy->question = Str::limit($lpFaq->question, 248, '').' (Copy)';
        $copy->order = ((int) LpFaq::query()->max('order')) + 1;
        $copy->save();

        return redirect()
            ->route('lp-faqs.index')
            ->with('success', __('FAQ berhasil diduplikasi.'));
    }

    /**
     * Remove the specified LP FAQ from storage.
     */
    public function destroy(LpFaq $lpFaq): RedirectResponse
    {
        Gate::authorize('lp-faqs.delete');

        $lpFaq->delete();

        return redirect()
            ->route('lp-faqs.index')
            ->with('success', __('FAQ berhasil dihapus.'));
    }

    /**
     * Export LP FAQs to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('lp-faqs.view');

        return Excel::download(new LpFaqsExport, 'lp-faqs-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * Export LP FAQs to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('lp-faqs.view');

        $faqs = LpFaq::query()->orderBy('order')->orderBy('id')->get();
        $pdf = Pdf::loadView('modules.lp-faqs.pdf', compact('faqs'));

        return $pdf->download('lp-faqs-'.now()->format('Y-m-d-His').'.pdf');
    }

    /**
     * Download sample Excel template for importing LP FAQs.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('lp-faqs.create');

        return Excel::download(new LpFaqsTemplateExport, 'lp-faqs-template.xlsx');
    }

    /**
     * Import LP FAQs from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('lp-faqs.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new LpFaqsImport, $request->file('file'));

        return redirect()
            ->route('lp-faqs.index')
            ->with('success', __('FAQ berhasil diimport.'));
    }

    /**
     * Validate request payload for LP FAQs.
     *
     * @return array<string, mixed>
     */
    private function validateFaq(Request $request): array
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['order'] = (int) ($validated['order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
