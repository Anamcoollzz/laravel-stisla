<?php

namespace App\Http\Controllers;

use App\Exports\ProductCategoriesExport;
use App\Exports\ProductCategoriesTemplateExport;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use App\Imports\ProductCategoriesImport;
use App\Models\ProductCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the product categories.
     */
    public function index(): View
    {
        Gate::authorize('product-categories.view');

        $categories = ProductCategory::query()
            ->with(['parent.parent', 'children.children'])
            ->get()
            ->sortBy(function (ProductCategory $c) {
                // Hierarchical sorting: root group, parent group, then order
                $p1 = $c->parent?->parent_id ? $c->parent->parent_id : ($c->parent_id ?: $c->id);
                $p2 = $c->parent?->parent_id ? $c->parent_id : ($c->parent_id ? $c->id : 0);
                $p3 = $c->parent?->parent_id ? $c->id : 0;

                return sprintf('%05d-%05d-%05d-%05d', $p1, $p2, $p3, $c->order);
            })
            ->values();

        $stats = [
            'total' => $categories->count(),
            'level1' => $categories->filter(fn ($c) => $c->level === 1)->count(),
            'level2' => $categories->filter(fn ($c) => $c->level === 2)->count(),
            'level3' => $categories->filter(fn ($c) => $c->level === 3)->count(),
        ];

        return view('modules.product-categories.index', [
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new product category.
     */
    public function create(): View
    {
        Gate::authorize('product-categories.create');

        // Only Level 1 & Level 2 can be parents (Max 3 levels)
        $potentialParents = ProductCategory::query()
            ->with('parent')
            ->whereNull('parent_id')
            ->orWhereHas('parent', fn ($q) => $q->whereNull('parent_id'))
            ->orderBy('order')
            ->get();

        $availableRoutes = $this->getAvailableEcommerceRoutes();

        return view('modules.product-categories.create', [
            'potentialParents' => $potentialParents,
            'availableRoutes' => $availableRoutes,
        ]);
    }

    /**
     * Store a newly created product category in storage.
     */
    public function store(StoreProductCategoryRequest $request): RedirectResponse
    {
        Gate::authorize('product-categories.create');

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $data['order'] ?? 0;

        if ($request->hasFile('icon_file')) {
            $data['icon'] = $request->file('icon_file')->store('product-categories', 'public');
        }

        if (empty($data['slug'])) {
            $data['slug'] = ProductCategory::generateUniqueSlug($data['name']);
        }

        unset($data['icon_file']);

        ProductCategory::query()->create($data);

        return redirect()
            ->route('product-categories.index')
            ->with('success', __('Product category created successfully.'));
    }

    /**
     * Display the specified product category.
     */
    public function show(ProductCategory $productCategory): View
    {
        Gate::authorize('product-categories.view');

        $productCategory->load(['parent.parent', 'children.children']);

        return view('modules.product-categories.show', [
            'category' => $productCategory,
        ]);
    }

    /**
     * Show the form for editing the specified product category.
     */
    public function edit(ProductCategory $productCategory): View
    {
        Gate::authorize('product-categories.edit');

        // Parents allowed: cannot be itself and cannot exceed 3 levels
        $descendantIds = $this->getDescendantIds($productCategory);
        $excludeIds = array_merge([$productCategory->id], $descendantIds);

        $potentialParents = ProductCategory::query()
            ->with('parent')
            ->whereNotIn('id', $excludeIds)
            ->where(function ($q) {
                $q->whereNull('parent_id')
                    ->orWhereHas('parent', fn ($sub) => $sub->whereNull('parent_id'));
            })
            ->orderBy('order')
            ->get();

        $availableRoutes = $this->getAvailableEcommerceRoutes();

        return view('modules.product-categories.edit', [
            'category' => $productCategory,
            'potentialParents' => $potentialParents,
            'availableRoutes' => $availableRoutes,
        ]);
    }

    /**
     * Update the specified product category in storage.
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory): RedirectResponse
    {
        Gate::authorize('product-categories.edit');

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['order'] = $data['order'] ?? 0;

        if ($request->hasFile('icon_file')) {
            $data['icon'] = $request->file('icon_file')->store('product-categories', 'public');
        }

        if (empty($data['slug'])) {
            $data['slug'] = ProductCategory::generateUniqueSlug($data['name'], $productCategory->id);
        }

        unset($data['icon_file']);

        $productCategory->update($data);

        return redirect()
            ->route('product-categories.index')
            ->with('success', __('Product category updated successfully.'));
    }

    /**
     * Duplicate the specified product category.
     */
    public function duplicate(ProductCategory $productCategory): RedirectResponse
    {
        Gate::authorize('product-categories.create');

        $copyName = $productCategory->name.' (Copy)';
        $copySlug = ProductCategory::generateUniqueSlug($copyName);

        ProductCategory::query()->create([
            'parent_id' => $productCategory->parent_id,
            'name' => $copyName,
            'slug' => $copySlug,
            'icon' => $productCategory->icon,
            'route' => $productCategory->route,
            'url' => $productCategory->url,
            'description' => $productCategory->description,
            'order' => $productCategory->order + 1,
            'is_active' => $productCategory->is_active,
        ]);

        return redirect()
            ->route('product-categories.index')
            ->with('success', __('Product category duplicated successfully.'));
    }

    /**
     * Remove the specified product category from storage.
     */
    public function destroy(ProductCategory $productCategory): RedirectResponse
    {
        Gate::authorize('product-categories.delete');

        $productCategory->delete();

        return redirect()
            ->route('product-categories.index')
            ->with('success', __('Product category deleted successfully.'));
    }

    /**
     * Export product categories to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('product-categories.view');

        return Excel::download(new ProductCategoriesExport, 'product_categories_'.date('Y-m-d_His').'.xlsx');
    }

    /**
     * Export product categories to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('product-categories.view');

        $categories = ProductCategory::query()
            ->with(['parent.parent'])
            ->get()
            ->sortBy(function (ProductCategory $c) {
                $p1 = $c->parent?->parent_id ? $c->parent->parent_id : ($c->parent_id ?: $c->id);
                $p2 = $c->parent?->parent_id ? $c->parent_id : ($c->parent_id ? $c->id : 0);
                $p3 = $c->parent?->parent_id ? $c->id : 0;

                return sprintf('%05d-%05d-%05d-%05d', $p1, $p2, $p3, $c->order);
            })
            ->values();

        return Pdf::loadView('modules.product-categories.pdf', [
            'categories' => $categories,
        ])->setPaper('a4', 'landscape')->download('product_categories_'.date('Y-m-d_His').'.pdf');
    }

    /**
     * Download product categories import template.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('product-categories.create');

        return Excel::download(new ProductCategoriesTemplateExport, 'product_categories_import_example.xlsx');
    }

    /**
     * Import product categories from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('product-categories.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            Excel::import(new ProductCategoriesImport, $request->file('file'));

            return redirect()
                ->route('product-categories.index')
                ->with('success', __('Data imported successfully.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('product-categories.index')
                ->with('error', __('Import failed: ').$e->getMessage());
        }
    }

    /**
     * Get all descendant IDs of a category recursively.
     *
     * @return array<int>
     */
    protected function getDescendantIds(ProductCategory $category): array
    {
        $ids = [];
        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getDescendantIds($child));
        }

        return $ids;
    }

    /**
     * Get available ecommerce routes.
     *
     * @return Collection<int, string>
     */
    protected function getAvailableEcommerceRoutes()
    {
        return collect(Route::getRoutes()->getRoutes())
            ->map(fn ($r) => $r->getName())
            ->filter(fn ($name) => $name && str_starts_with($name, 'ecommerce.'))
            ->unique()
            ->sort()
            ->values();
    }
}
