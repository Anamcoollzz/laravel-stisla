<?php

namespace App\Http\Controllers;

use App\Exports\EcommerceFaqsExport;
use App\Exports\EcommerceFaqsTemplateExport;
use App\Http\Requests\StoreEcommerceFaqRequest;
use App\Http\Requests\UpdateEcommerceFaqRequest;
use App\Imports\EcommerceFaqsImport;
use App\Models\EcommerceFaq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EcommerceFaqController extends Controller
{
    /**
     * Display a listing of ecommerce FAQs.
     */
    public function index(): View
    {
        Gate::authorize('ecommerce-faqs.view');

        $faqs = EcommerceFaq::query()->orderBy('column')->orderBy('order')->latest('id')->get();

        $stats = [
            'total' => $faqs->count(),
            'active' => $faqs->where('is_active', true)->count(),
            'inactive' => $faqs->where('is_active', false)->count(),
        ];

        return view('modules.ecommerce-faqs.index', compact('faqs', 'stats'));
    }

    /**
     * Show the form for creating a new FAQ.
     */
    public function create(): View
    {
        Gate::authorize('ecommerce-faqs.create');

        return view('modules.ecommerce-faqs.create');
    }

    /**
     * Store a newly created FAQ in storage.
     */
    public function store(StoreEcommerceFaqRequest $request): RedirectResponse
    {
        Gate::authorize('ecommerce-faqs.create');

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $data['order'] ?? (EcommerceFaq::where('column', $data['column'])->max('order') + 1);

        EcommerceFaq::create($data);

        return redirect()->route('ecommerce-faqs.index')->with('success', __('FAQ created successfully.'));
    }

    /**
     * Display the specified FAQ.
     */
    public function show(EcommerceFaq $ecommerceFaq): View
    {
        Gate::authorize('ecommerce-faqs.view');

        return view('modules.ecommerce-faqs.show', ['faq' => $ecommerceFaq]);
    }

    /**
     * Show the form for editing the specified FAQ.
     */
    public function edit(EcommerceFaq $ecommerceFaq): View
    {
        Gate::authorize('ecommerce-faqs.edit');

        return view('modules.ecommerce-faqs.edit', ['faq' => $ecommerceFaq]);
    }

    /**
     * Update the specified FAQ in storage.
     */
    public function update(UpdateEcommerceFaqRequest $request, EcommerceFaq $ecommerceFaq): RedirectResponse
    {
        Gate::authorize('ecommerce-faqs.edit');

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $ecommerceFaq->update($data);

        return redirect()->route('ecommerce-faqs.index')->with('success', __('FAQ updated successfully.'));
    }

    /**
     * Remove the specified FAQ from storage.
     */
    public function destroy(EcommerceFaq $ecommerceFaq): RedirectResponse
    {
        Gate::authorize('ecommerce-faqs.delete');

        $ecommerceFaq->delete();

        return redirect()->route('ecommerce-faqs.index')->with('success', __('FAQ deleted successfully.'));
    }

    /**
     * Duplicate the specified FAQ.
     */
    public function duplicate(EcommerceFaq $ecommerceFaq): RedirectResponse
    {
        Gate::authorize('ecommerce-faqs.create');

        $clone = $ecommerceFaq->replicate();
        $clone->question = $ecommerceFaq->question.' (Copy)';
        $clone->order = (EcommerceFaq::where('column', $ecommerceFaq->column)->max('order') ?? 0) + 1;
        $clone->save();

        return redirect()->route('ecommerce-faqs.index')->with('success', __('FAQ duplicated successfully.'));
    }

    /**
     * Export FAQs to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('ecommerce-faqs.view');

        return Excel::download(new EcommerceFaqsExport, 'ecommerce-faqs-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * Download sample Excel template for importing FAQs.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('ecommerce-faqs.create');

        return Excel::download(new EcommerceFaqsTemplateExport, 'ecommerce-faqs-template.xlsx');
    }

    /**
     * Import FAQs from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('ecommerce-faqs.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new EcommerceFaqsImport, $request->file('file'));

        return redirect()->route('ecommerce-faqs.index')->with('success', __('FAQs imported successfully.'));
    }
}
