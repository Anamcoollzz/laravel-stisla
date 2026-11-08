<?php

namespace App\Http\Controllers;

use App\Exports\ProductColorsExport;
use App\Exports\ProductColorsTemplateExport;
use App\Http\Requests\StoreProductColorRequest;
use App\Http\Requests\UpdateProductColorRequest;
use App\Imports\ProductColorsImport;
use App\Models\ProductColor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductColorController extends Controller
{
    public function index(): View
    {
        Gate::authorize('product-colors.view');

        $colors = ProductColor::query()->withCount('products')->get();

        $stats = [
            'total' => $colors->count(),
            'total_products' => $colors->sum('products_count'),
        ];

        return view('modules.product-colors.index', compact('colors', 'stats'));
    }

    public function create(): View
    {
        Gate::authorize('product-colors.create');

        return view('modules.product-colors.create');
    }

    public function store(StoreProductColorRequest $request): RedirectResponse
    {
        Gate::authorize('product-colors.create');

        ProductColor::create($request->validated());

        return redirect()->route('product-colors.index')->with('success', __('Color created successfully.'));
    }

    public function show(ProductColor $productColor): View
    {
        Gate::authorize('product-colors.view');

        $productColor->load(['products' => fn ($q) => $q->latest('id')]);

        return view('modules.product-colors.show', ['color' => $productColor]);
    }

    public function edit(ProductColor $productColor): View
    {
        Gate::authorize('product-colors.edit');

        return view('modules.product-colors.edit', ['color' => $productColor]);
    }

    public function update(UpdateProductColorRequest $request, ProductColor $productColor): RedirectResponse
    {
        Gate::authorize('product-colors.edit');

        $productColor->update($request->validated());

        return redirect()->route('product-colors.index')->with('success', __('Color updated successfully.'));
    }

    public function duplicate(ProductColor $productColor): RedirectResponse
    {
        Gate::authorize('product-colors.create');

        $copyName = $productColor->name.' (Copy)';

        ProductColor::create([
            'name' => $copyName,
            'color_code' => $productColor->color_code,
        ]);

        return redirect()->route('product-colors.index')->with('success', __('Color duplicated successfully.'));
    }

    public function destroy(ProductColor $productColor): RedirectResponse
    {
        Gate::authorize('product-colors.delete');

        $productColor->delete();

        return redirect()->route('product-colors.index')->with('success', __('Color deleted successfully.'));
    }

    public function export(): BinaryFileResponse
    {
        Gate::authorize('product-colors.view');

        return Excel::download(new ProductColorsExport, 'product_colors_'.date('Y-m-d_His').'.xlsx');
    }

    public function exportPdf(): Response
    {
        Gate::authorize('product-colors.view');

        $colors = ProductColor::query()->withCount('products')->get();

        return Pdf::loadView('modules.product-colors.pdf', compact('colors'))
            ->setPaper('a4', 'portrait')
            ->download('product_colors_'.date('Y-m-d_His').'.pdf');
    }

    public function template(): BinaryFileResponse
    {
        Gate::authorize('product-colors.create');

        return Excel::download(new ProductColorsTemplateExport, 'product_colors_import_example.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('product-colors.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            Excel::import(new ProductColorsImport, $request->file('file'));

            return redirect()->route('product-colors.index')->with('success', __('Data imported successfully.'));
        } catch (\Throwable $e) {
            return redirect()->route('product-colors.index')->with('error', __('Import failed: ').$e->getMessage());
        }
    }
}
