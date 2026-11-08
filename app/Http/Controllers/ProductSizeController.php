<?php

namespace App\Http\Controllers;

use App\Exports\ProductSizesExport;
use App\Exports\ProductSizesTemplateExport;
use App\Http\Requests\StoreProductSizeRequest;
use App\Http\Requests\UpdateProductSizeRequest;
use App\Imports\ProductSizesImport;
use App\Models\ProductSize;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductSizeController extends Controller
{
    public function index(): View
    {
        Gate::authorize('product-sizes.view');

        $sizes = ProductSize::query()->withCount('products')->get();

        $stats = [
            'total' => $sizes->count(),
            'total_products' => $sizes->sum('products_count'),
        ];

        return view('modules.product-sizes.index', compact('sizes', 'stats'));
    }

    public function create(): View
    {
        Gate::authorize('product-sizes.create');

        return view('modules.product-sizes.create');
    }

    public function store(StoreProductSizeRequest $request): RedirectResponse
    {
        Gate::authorize('product-sizes.create');

        $data = $request->validated();
        if (empty($data['code'])) {
            $data['code'] = strtoupper($data['name']);
        }

        ProductSize::create($data);

        return redirect()->route('product-sizes.index')->with('success', __('Size created successfully.'));
    }

    public function show(ProductSize $productSize): View
    {
        Gate::authorize('product-sizes.view');

        $productSize->load(['products' => fn ($q) => $q->latest('id')]);

        return view('modules.product-sizes.show', ['size' => $productSize]);
    }

    public function edit(ProductSize $productSize): View
    {
        Gate::authorize('product-sizes.edit');

        return view('modules.product-sizes.edit', ['size' => $productSize]);
    }

    public function update(UpdateProductSizeRequest $request, ProductSize $productSize): RedirectResponse
    {
        Gate::authorize('product-sizes.edit');

        $data = $request->validated();
        if (empty($data['code'])) {
            $data['code'] = strtoupper($data['name']);
        }

        $productSize->update($data);

        return redirect()->route('product-sizes.index')->with('success', __('Size updated successfully.'));
    }

    public function duplicate(ProductSize $productSize): RedirectResponse
    {
        Gate::authorize('product-sizes.create');

        $copyName = $productSize->name.' (Copy)';

        ProductSize::create([
            'name' => $copyName,
            'code' => $productSize->code ? $productSize->code.'-CPY' : strtoupper($copyName),
        ]);

        return redirect()->route('product-sizes.index')->with('success', __('Size duplicated successfully.'));
    }

    public function destroy(ProductSize $productSize): RedirectResponse
    {
        Gate::authorize('product-sizes.delete');

        $productSize->delete();

        return redirect()->route('product-sizes.index')->with('success', __('Size deleted successfully.'));
    }

    public function export(): BinaryFileResponse
    {
        Gate::authorize('product-sizes.view');

        return Excel::download(new ProductSizesExport, 'product_sizes_'.date('Y-m-d_His').'.xlsx');
    }

    public function exportPdf(): Response
    {
        Gate::authorize('product-sizes.view');

        $sizes = ProductSize::query()->withCount('products')->get();

        return Pdf::loadView('modules.product-sizes.pdf', compact('sizes'))
            ->setPaper('a4', 'portrait')
            ->download('product_sizes_'.date('Y-m-d_His').'.pdf');
    }

    public function template(): BinaryFileResponse
    {
        Gate::authorize('product-sizes.create');

        return Excel::download(new ProductSizesTemplateExport, 'product_sizes_import_example.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('product-sizes.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            Excel::import(new ProductSizesImport, $request->file('file'));

            return redirect()->route('product-sizes.index')->with('success', __('Data imported successfully.'));
        } catch (\Throwable $e) {
            return redirect()->route('product-sizes.index')->with('error', __('Import failed: ').$e->getMessage());
        }
    }
}
