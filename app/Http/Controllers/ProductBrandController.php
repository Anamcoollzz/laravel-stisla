<?php

namespace App\Http\Controllers;

use App\Exports\ProductBrandsExport;
use App\Exports\ProductBrandsTemplateExport;
use App\Http\Requests\StoreProductBrandRequest;
use App\Http\Requests\UpdateProductBrandRequest;
use App\Imports\ProductBrandsImport;
use App\Models\ProductBrand;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductBrandController extends Controller
{
    public function index(): View
    {
        Gate::authorize('product-brands.view');

        $brands = ProductBrand::query()->withCount('products')->latest('id')->get();

        $stats = [
            'total' => $brands->count(),
            'total_products' => $brands->sum('products_count'),
        ];

        return view('modules.product-brands.index', compact('brands', 'stats'));
    }

    public function create(): View
    {
        Gate::authorize('product-brands.create');

        return view('modules.product-brands.create');
    }

    public function store(StoreProductBrandRequest $request): RedirectResponse
    {
        Gate::authorize('product-brands.create');

        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        ProductBrand::create($data);

        return redirect()->route('product-brands.index')->with('success', __('Brand created successfully.'));
    }

    public function show(ProductBrand $productBrand): View
    {
        Gate::authorize('product-brands.view');

        $productBrand->load(['products' => fn ($q) => $q->latest('id')]);

        return view('modules.product-brands.show', ['brand' => $productBrand]);
    }

    public function edit(ProductBrand $productBrand): View
    {
        Gate::authorize('product-brands.edit');

        return view('modules.product-brands.edit', ['brand' => $productBrand]);
    }

    public function update(UpdateProductBrandRequest $request, ProductBrand $productBrand): RedirectResponse
    {
        Gate::authorize('product-brands.edit');

        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $productBrand->update($data);

        return redirect()->route('product-brands.index')->with('success', __('Brand updated successfully.'));
    }

    public function duplicate(ProductBrand $productBrand): RedirectResponse
    {
        Gate::authorize('product-brands.create');

        $copyName = $productBrand->name.' (Copy)';
        $copySlug = Str::slug($copyName);

        ProductBrand::create([
            'name' => $copyName,
            'slug' => $copySlug,
        ]);

        return redirect()->route('product-brands.index')->with('success', __('Brand duplicated successfully.'));
    }

    public function destroy(ProductBrand $productBrand): RedirectResponse
    {
        Gate::authorize('product-brands.delete');

        $productBrand->delete();

        return redirect()->route('product-brands.index')->with('success', __('Brand deleted successfully.'));
    }

    public function export(): BinaryFileResponse
    {
        Gate::authorize('product-brands.view');

        return Excel::download(new ProductBrandsExport, 'product_brands_'.date('Y-m-d_His').'.xlsx');
    }

    public function exportPdf(): Response
    {
        Gate::authorize('product-brands.view');

        $brands = ProductBrand::query()->withCount('products')->latest('id')->get();

        return Pdf::loadView('modules.product-brands.pdf', compact('brands'))
            ->setPaper('a4', 'portrait')
            ->download('product_brands_'.date('Y-m-d_His').'.pdf');
    }

    public function template(): BinaryFileResponse
    {
        Gate::authorize('product-brands.create');

        return Excel::download(new ProductBrandsTemplateExport, 'product_brands_import_example.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('product-brands.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            Excel::import(new ProductBrandsImport, $request->file('file'));

            return redirect()->route('product-brands.index')->with('success', __('Data imported successfully.'));
        } catch (\Throwable $e) {
            return redirect()->route('product-brands.index')->with('error', __('Import failed: ').$e->getMessage());
        }
    }
}
