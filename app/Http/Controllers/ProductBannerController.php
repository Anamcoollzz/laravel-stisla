<?php

namespace App\Http\Controllers;

use App\Exports\ProductBannersExport;
use App\Exports\ProductBannersTemplateExport;
use App\Http\Requests\StoreProductBannerRequest;
use App\Http\Requests\UpdateProductBannerRequest;
use App\Imports\ProductBannersImport;
use App\Models\ProductBanner;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductBannerController extends Controller
{
    public function index(): View
    {
        Gate::authorize('product-banners.view');

        $banners = ProductBanner::query()->orderBy('order')->latest('id')->get();

        $stats = [
            'total' => $banners->count(),
            'active' => $banners->where('is_active', true)->count(),
            'hero' => $banners->where('position', 'hero')->count(),
        ];

        return view('modules.product-banners.index', compact('banners', 'stats'));
    }

    public function create(): View
    {
        Gate::authorize('product-banners.create');

        return view('modules.product-banners.create');
    }

    public function store(StoreProductBannerRequest $request): RedirectResponse
    {
        Gate::authorize('product-banners.create');

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('product-banners', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $data['order'] ?? (ProductBanner::max('order') + 1);

        ProductBanner::create($data);

        return redirect()->route('product-banners.index')->with('success', __('Banner created successfully.'));
    }

    public function show(ProductBanner $productBanner): View
    {
        Gate::authorize('product-banners.view');

        return view('modules.product-banners.show', ['banner' => $productBanner]);
    }

    public function edit(ProductBanner $productBanner): View
    {
        Gate::authorize('product-banners.edit');

        return view('modules.product-banners.edit', ['banner' => $productBanner]);
    }

    public function update(UpdateProductBannerRequest $request, ProductBanner $productBanner): RedirectResponse
    {
        Gate::authorize('product-banners.edit');

        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($productBanner->image && Storage::disk('public')->exists($productBanner->image)) {
                Storage::disk('public')->delete($productBanner->image);
            }
            $data['image'] = $request->file('image')->store('product-banners', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($productBanner->image && Storage::disk('public')->exists($productBanner->image)) {
                Storage::disk('public')->delete($productBanner->image);
            }
            $data['image'] = null;
        }

        $data['is_active'] = $request->boolean('is_active');

        $productBanner->update($data);

        return redirect()->route('product-banners.index')->with('success', __('Banner updated successfully.'));
    }

    public function destroy(ProductBanner $productBanner): RedirectResponse
    {
        Gate::authorize('product-banners.delete');

        if ($productBanner->image && Storage::disk('public')->exists($productBanner->image)) {
            Storage::disk('public')->delete($productBanner->image);
        }

        $productBanner->delete();

        return redirect()->route('product-banners.index')->with('success', __('Banner deleted successfully.'));
    }

    public function duplicate(ProductBanner $productBanner): RedirectResponse
    {
        Gate::authorize('product-banners.create');

        $clone = $productBanner->replicate();
        $clone->title = $productBanner->title.' (Copy)';
        $clone->order = (ProductBanner::max('order') ?? 0) + 1;
        $clone->save();

        return redirect()->route('product-banners.index')->with('success', __('Banner duplicated successfully.'));
    }

    public function export(): BinaryFileResponse
    {
        Gate::authorize('product-banners.view');

        return Excel::download(new ProductBannersExport, 'product-banners-'.now()->format('Y-m-d-His').'.xlsx');
    }

    public function exportPdf(): Response
    {
        Gate::authorize('product-banners.view');

        $banners = ProductBanner::query()->orderBy('order')->latest('id')->get();

        $pdf = Pdf::loadView('modules.product-banners.pdf', compact('banners'));

        return $pdf->download('product-banners-'.now()->format('Y-m-d-His').'.pdf');
    }

    public function template(): BinaryFileResponse
    {
        Gate::authorize('product-banners.create');

        return Excel::download(new ProductBannersTemplateExport, 'product-banners-template.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('product-banners.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new ProductBannersImport, $request->file('file'));

        return redirect()->route('product-banners.index')->with('success', __('Banners imported successfully.'));
    }
}
