<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductColor;
use App\Models\ProductSize;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    /**
     * Display the specified product detail page.
     */
    public function show(string $slug): View
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->with(['category', 'brand', 'vendor', 'size', 'color', 'images'])
            ->firstOrFail();

        $relatedProducts = Product::query()
            ->active()
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->with(['category', 'brand', 'size', 'color'])
            ->latest('id')
            ->take(8)
            ->get();

        if ($relatedProducts->count() < 4) {
            $moreProducts = Product::query()
                ->active()
                ->where('id', '!=', $product->id)
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->with(['category', 'brand', 'size', 'color'])
                ->latest('id')
                ->take(8 - $relatedProducts->count())
                ->get();

            $relatedProducts = $relatedProducts->concat($moreProducts);
        }

        $sidebarCategories = ProductCategory::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->withCount('products')
            ->orderBy('order')
            ->get();

        $sidebarBrands = ProductBrand::query()
            ->orderBy('name')
            ->get();

        $sidebarSizes = ProductSize::query()
            ->orderBy('id')
            ->get();

        $sidebarColors = ProductColor::query()
            ->orderBy('name')
            ->get();

        return view('ecommerce.pages.product-detail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'sidebarCategories' => $sidebarCategories,
            'sidebarBrands' => $sidebarBrands,
            'sidebarSizes' => $sidebarSizes,
            'sidebarColors' => $sidebarColors,
        ]);
    }

    /**
     * Detail alias for show.
     */
    public function detail(string $slug): View
    {
        return $this->show($slug);
    }
}
