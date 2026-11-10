<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductColor;
use App\Models\ProductSize;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products with optional category, brand, size, color and price filters.
     */
    public function index(Request $request): View
    {
        $categorySlug = $request->query('category');
        $currentCategory = null;

        $query = Product::query()
            ->active()
            ->with(['category', 'brand', 'size', 'color', 'images']);

        if ($categorySlug) {
            $currentCategory = ProductCategory::query()
                ->where('slug', $categorySlug)
                ->first();

            if ($currentCategory) {
                $categoryIds = collect([$currentCategory->id]);
                $childIds = ProductCategory::where('parent_id', $currentCategory->id)->pluck('id');
                $grandChildIds = ProductCategory::whereIn('parent_id', $childIds)->pluck('id');
                $categoryIds = $categoryIds->merge($childIds)->merge($grandChildIds);

                $query->whereIn('category_id', $categoryIds);
            }
        }

        if ($request->filled('brand')) {
            $brandParam = $request->query('brand');
            $query->whereHas('brand', function ($q) use ($brandParam) {
                if (is_numeric($brandParam)) {
                    $q->where('id', (int) $brandParam)->orWhere('slug', (string) $brandParam);
                } else {
                    $q->where('slug', (string) $brandParam);
                }
            });
        }

        if ($request->filled('size')) {
            $sizeParam = $request->query('size');
            $query->whereHas('size', function ($q) use ($sizeParam) {
                if (is_numeric($sizeParam)) {
                    $q->where('id', (int) $sizeParam)->orWhere('code', (string) $sizeParam);
                } else {
                    $q->where('code', (string) $sizeParam)->orWhere('name', (string) $sizeParam);
                }
            });
        }

        if ($request->filled('color')) {
            $colorParam = $request->query('color');
            $query->whereHas('color', function ($q) use ($colorParam) {
                if (is_numeric($colorParam)) {
                    $q->where('id', (int) $colorParam);
                } else {
                    $q->where('name', (string) $colorParam);
                }
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->query('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->query('max_price'));
        }

        if ($request->filled('q')) {
            $search = trim((string) $request->query('q'));
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%");
                });

                // Store search query into session history
                $recentSearches = (array) session('recent_searches', []);
                $recentSearches = array_values(array_filter(array_unique(array_merge([$search], $recentSearches))));
                session(['recent_searches' => array_slice($recentSearches, 0, 6)]);
            }
        }

        $sort = $request->query('sort', 'latest');
        match ($sort) {
            '2', 'name_asc' => $query->orderBy('title', 'asc'),
            '3', 'name_desc' => $query->orderBy('title', 'desc'),
            '4', 'price_low' => $query->orderBy('price', 'asc'),
            '5', 'price_high' => $query->orderBy('price', 'desc'),
            default => $query->latest('id'),
        };

        $products = $query->paginate(12)->withQueryString();

        $categories = ProductCategory::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with([
                'children' => fn ($q) => $q->where('is_active', true)->withCount('products'),
            ])
            ->withCount('products')
            ->orderBy('order')
            ->get()
            ->unique('name')
            ->values()
            ->map(function ($category) {
                if ($category->relationLoaded('children')) {
                    $category->setRelation('children', $category->children->unique('name')->values());
                }

                return $category;
            });

        $brands = ProductBrand::query()
            ->withCount('products')
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->values();

        $sizes = ProductSize::query()
            ->withCount('products')
            ->orderBy('id')
            ->get()
            ->unique('name')
            ->values();

        $colors = ProductColor::query()
            ->withCount('products')
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->values();

        return view('ecommerce.pages.shop', [
            'products' => $products,
            'currentCategory' => $currentCategory,
            'categorySlug' => $categorySlug,
            'categories' => $categories,
            'brands' => $brands,
            'sizes' => $sizes,
            'colors' => $colors,
            'sort' => $sort,
        ]);
    }

    /**
     * Display products for a specific category.
     */
    public function category(ProductCategory $category): View
    {
        return $this->index(request()->merge(['category' => $category->slug]));
    }

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
            ->get()
            ->unique('name')
            ->values();

        $sidebarBrands = ProductBrand::query()
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->values();

        $sidebarSizes = ProductSize::query()
            ->orderBy('id')
            ->get()
            ->unique('name')
            ->values();

        $sidebarColors = ProductColor::query()
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->values();

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

    /**
     * Clear recent searches from session.
     */
    public function clearRecentSearches(): JsonResponse
    {
        session()->forget('recent_searches');

        return response()->json(['success' => true]);
    }
}
