<?php

namespace App\Http\Controllers;

use App\Exports\ProductVendorsExport;
use App\Exports\ProductVendorsTemplateExport;
use App\Http\Requests\StoreProductVendorRequest;
use App\Http\Requests\UpdateProductVendorRequest;
use App\Imports\ProductVendorsImport;
use App\Models\ProductVendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductVendorController extends Controller
{
    public function index(): View
    {
        Gate::authorize('product-vendors.view');

        $vendors = ProductVendor::query()->withCount('products')->latest('id')->get();

        $stats = [
            'total' => $vendors->count(),
            'total_products' => $vendors->sum('products_count'),
        ];

        return view('modules.product-vendors.index', compact('vendors', 'stats'));
    }

    public function create(): View
    {
        Gate::authorize('product-vendors.create');

        return view('modules.product-vendors.create');
    }

    public function store(StoreProductVendorRequest $request): RedirectResponse
    {
        Gate::authorize('product-vendors.create');

        ProductVendor::create($request->validated());

        return redirect()->route('product-vendors.index')->with('success', __('Vendor created successfully.'));
    }

    public function show(ProductVendor $productVendor): View
    {
        Gate::authorize('product-vendors.view');

        $productVendor->load(['products' => fn ($q) => $q->latest('id')]);

        return view('modules.product-vendors.show', ['vendor' => $productVendor]);
    }

    public function edit(ProductVendor $productVendor): View
    {
        Gate::authorize('product-vendors.edit');

        return view('modules.product-vendors.edit', ['vendor' => $productVendor]);
    }

    public function update(UpdateProductVendorRequest $request, ProductVendor $productVendor): RedirectResponse
    {
        Gate::authorize('product-vendors.edit');

        $productVendor->update($request->validated());

        return redirect()->route('product-vendors.index')->with('success', __('Vendor updated successfully.'));
    }

    public function duplicate(ProductVendor $productVendor): RedirectResponse
    {
        Gate::authorize('product-vendors.create');

        ProductVendor::create([
            'name' => $productVendor->name.' (Copy)',
            'phone' => $productVendor->phone,
            'email' => $productVendor->email,
            'address' => $productVendor->address,
            'description' => $productVendor->description,
        ]);

        return redirect()->route('product-vendors.index')->with('success', __('Vendor duplicated successfully.'));
    }

    public function destroy(ProductVendor $productVendor): RedirectResponse
    {
        Gate::authorize('product-vendors.delete');

        $productVendor->delete();

        return redirect()->route('product-vendors.index')->with('success', __('Vendor deleted successfully.'));
    }

    public function export(): BinaryFileResponse
    {
        Gate::authorize('product-vendors.view');

        return Excel::download(new ProductVendorsExport, 'product_vendors_'.date('Y-m-d_His').'.xlsx');
    }

    public function exportPdf(): Response
    {
        Gate::authorize('product-vendors.view');

        $vendors = ProductVendor::query()->withCount('products')->latest('id')->get();

        return Pdf::loadView('modules.product-vendors.pdf', compact('vendors'))
            ->setPaper('a4', 'landscape')
            ->download('product_vendors_'.date('Y-m-d_His').'.pdf');
    }

    public function template(): BinaryFileResponse
    {
        Gate::authorize('product-vendors.create');

        return Excel::download(new ProductVendorsTemplateExport, 'product_vendors_import_example.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('product-vendors.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            Excel::import(new ProductVendorsImport, $request->file('file'));

            return redirect()->route('product-vendors.index')->with('success', __('Data imported successfully.'));
        } catch (\Throwable $e) {
            return redirect()->route('product-vendors.index')->with('error', __('Import failed: ').$e->getMessage());
        }
    }
}
