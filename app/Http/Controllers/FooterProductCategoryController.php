<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFooterProductCategoryRequest;
use App\Http\Requests\UpdateFooterProductCategoryRequest;
use App\Models\FooterProductCategory;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FooterProductCategoryController extends Controller
{
    /**
     * Display a listing of footer product categories.
     */
    public function index(): View
    {
        Gate::authorize('footer-product-categories.view');

        $footerCategories = FooterProductCategory::query()
            ->with('category.parent')
            ->orderBy('order')
            ->latest('id')
            ->get();

        $stats = [
            'total' => $footerCategories->count(),
            'active' => $footerCategories->where('is_active', true)->count(),
            'inactive' => $footerCategories->where('is_active', false)->count(),
        ];

        return view('modules.footer-product-categories.index', compact('footerCategories', 'stats'));
    }

    /**
     * Show the form for creating a new footer product category.
     */
    public function create(): View
    {
        Gate::authorize('footer-product-categories.create');

        $categories = ProductCategory::query()
            ->with('parent')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('modules.footer-product-categories.create', compact('categories'));
    }

    /**
     * Store a newly created footer product category in storage.
     */
    public function store(StoreFooterProductCategoryRequest $request): RedirectResponse
    {
        Gate::authorize('footer-product-categories.create');

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $data['order'] ?? (FooterProductCategory::max('order') + 1);

        FooterProductCategory::create($data);

        return redirect()->route('footer-product-categories.index')->with('success', __('Footer category created successfully.'));
    }

    /**
     * Display the specified footer product category.
     */
    public function show(FooterProductCategory $footerProductCategory): View
    {
        Gate::authorize('footer-product-categories.view');

        $footerProductCategory->load('category.parent');

        return view('modules.footer-product-categories.show', ['footerCategory' => $footerProductCategory]);
    }

    /**
     * Show the form for editing the specified footer product category.
     */
    public function edit(FooterProductCategory $footerProductCategory): View
    {
        Gate::authorize('footer-product-categories.edit');

        $categories = ProductCategory::query()
            ->with('parent')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('modules.footer-product-categories.edit', [
            'footerCategory' => $footerProductCategory,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified footer product category in storage.
     */
    public function update(UpdateFooterProductCategoryRequest $request, FooterProductCategory $footerProductCategory): RedirectResponse
    {
        Gate::authorize('footer-product-categories.edit');

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $footerProductCategory->update($data);

        return redirect()->route('footer-product-categories.index')->with('success', __('Footer category updated successfully.'));
    }

    /**
     * Remove the specified footer product category from storage.
     */
    public function destroy(FooterProductCategory $footerProductCategory): RedirectResponse
    {
        Gate::authorize('footer-product-categories.delete');

        $footerProductCategory->delete();

        return redirect()->route('footer-product-categories.index')->with('success', __('Footer category deleted successfully.'));
    }
}
