<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Exports\ProductsTemplateExport;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Imports\ProductsImport;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductVendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request): View
    {
        Gate::authorize('products.view');

        $query = Product::query()->with(['category', 'brand', 'vendor', 'size', 'color']);

        // Filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->where('stock', '>', 0)->where('stock', '<=', 10);
            } elseif ($request->stock_status === 'in_stock') {
                $query->where('stock', '>', 10);
            }
        }

        $products = $query->latest('id')->get();

        $allProducts = Product::query()->get();
        $stats = [
            'total' => $allProducts->count(),
            'in_stock' => $allProducts->filter(fn ($p) => $p->stock > 0)->count(),
            'low_stock' => $allProducts->filter(fn ($p) => $p->stock <= 10)->count(),
            'active' => $allProducts->filter(fn ($p) => $p->is_active)->count(),
        ];

        $categories = ProductCategory::query()->orderBy('order')->get();
        $brands = ProductBrand::query()->orderBy('name')->get();
        $vendors = ProductVendor::query()->orderBy('name')->get();

        return view('modules.products.index', [
            'products' => $products,
            'stats' => $stats,
            'categories' => $categories,
            'brands' => $brands,
            'vendors' => $vendors,
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        Gate::authorize('products.create');

        return view('modules.products.create', $this->getFormData());
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        Gate::authorize('products.create');

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_trending'] = $request->boolean('is_trending');
        $data['is_new'] = $request->boolean('is_new');
        $data['is_sale'] = $request->boolean('is_sale');
        $data['is_deal'] = $request->boolean('is_deal');
        $data['discount_percent'] = $data['discount_percent'] ?? 0;

        // Thumbnail file upload
        if ($request->hasFile('thumbnail_file')) {
            $data['thumbnail'] = $request->file('thumbnail_file')->store('products', 'public');
        }
        unset($data['thumbnail_file']);

        // SKU
        if (empty($data['sku'])) {
            $data['sku'] = Product::generateUniqueSku();
        }

        // Slug
        if (empty($data['slug'])) {
            $data['slug'] = Product::generateUniqueSlug($data['title']);
        }

        // Tags parsing
        $data['tags'] = $this->parseTags($request->input('tags'));

        // Specifications parsing
        $data['spesifications'] = $this->parseSpecifications($request);

        unset($data['product_images']);

        $product = Product::query()->create($data);

        // Handle multiple product gallery images
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $index => $imageFile) {
                if ($imageFile->isValid()) {
                    $path = $imageFile->store('products/gallery', 'public');
                    $product->images()->create([
                        'image' => $path,
                        'order' => $index,
                        'is_primary' => false,
                    ]);
                }
            }
        }

        return redirect()
            ->route('products.index')
            ->with('success', __('Product created successfully.'));
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        Gate::authorize('products.view');

        $product->load(['category.parent.parent', 'brand', 'vendor', 'size', 'color', 'images']);

        return view('modules.products.show', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        Gate::authorize('products.edit');

        $product->load('images');

        return view('modules.products.edit', array_merge($this->getFormData(), [
            'product' => $product,
        ]));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        Gate::authorize('products.edit');

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['is_trending'] = $request->boolean('is_trending');
        $data['is_new'] = $request->boolean('is_new');
        $data['is_sale'] = $request->boolean('is_sale');
        $data['is_deal'] = $request->boolean('is_deal');
        $data['discount_percent'] = $data['discount_percent'] ?? 0;

        // Thumbnail file upload
        if ($request->hasFile('thumbnail_file')) {
            if ($product->thumbnail && ! str_starts_with($product->thumbnail, 'http') && ! str_starts_with($product->thumbnail, 'mantu/')) {
                Storage::disk('public')->delete($product->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail_file')->store('products', 'public');
        }
        unset($data['thumbnail_file']);

        // SKU
        if (empty($data['sku'])) {
            $data['sku'] = $product->sku ?: Product::generateUniqueSku();
        }

        // Slug
        if (empty($data['slug'])) {
            $data['slug'] = Product::generateUniqueSlug($data['title'], $product->id);
        }

        // Tags parsing
        $data['tags'] = $this->parseTags($request->input('tags'));

        // Specifications parsing
        $data['spesifications'] = $this->parseSpecifications($request);

        unset($data['product_images'], $data['delete_image_ids']);

        $product->update($data);

        // Handle deletion of existing gallery images
        if ($request->filled('delete_image_ids')) {
            $deleteIds = (array) $request->input('delete_image_ids', []);
            $imagesToDelete = $product->images()->whereIn('id', $deleteIds)->get();
            foreach ($imagesToDelete as $img) {
                if ($img->image && ! str_starts_with($img->image, 'http') && ! str_starts_with($img->image, 'mantu/')) {
                    Storage::disk('public')->delete($img->image);
                }
                $img->delete();
            }
        }

        // Handle upload of new gallery images
        if ($request->hasFile('product_images')) {
            $currentMaxOrder = $product->images()->max('order') ?? -1;
            foreach ($request->file('product_images') as $index => $imageFile) {
                if ($imageFile->isValid()) {
                    $path = $imageFile->store('products/gallery', 'public');
                    $product->images()->create([
                        'image' => $path,
                        'order' => $currentMaxOrder + 1 + $index,
                        'is_primary' => false,
                    ]);
                }
            }
        }

        return redirect()
            ->route('products.index')
            ->with('success', __('Product updated successfully.'));
    }

    /**
     * Duplicate the specified product.
     */
    public function duplicate(Product $product): RedirectResponse
    {
        Gate::authorize('products.create');

        $copyTitle = $product->title.' (Copy)';
        $copySlug = Product::generateUniqueSlug($copyTitle);
        $copySku = Product::generateUniqueSku();

        $newProduct = Product::query()->create([
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
            'vendor_id' => $product->vendor_id,
            'size_id' => $product->size_id,
            'color_id' => $product->color_id,
            'title' => $copyTitle,
            'slug' => $copySlug,
            'sku' => $copySku,
            'price' => $product->price,
            'discount_percent' => $product->discount_percent,
            'stock' => $product->stock,
            'short_description' => $product->short_description,
            'description_detail' => $product->description_detail,
            'spesifications' => $product->spesifications,
            'tags' => $product->tags,
            'thumbnail' => $product->thumbnail,
            'is_active' => $product->is_active,
            'is_trending' => $product->is_trending,
            'is_new' => $product->is_new,
            'is_sale' => $product->is_sale,
            'is_deal' => $product->is_deal,
        ]);

        foreach ($product->images as $img) {
            $newProduct->images()->create([
                'image' => $img->image,
                'order' => $img->order,
                'is_primary' => $img->is_primary,
            ]);
        }

        return redirect()
            ->route('products.index')
            ->with('success', __('Product duplicated successfully.'));
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        Gate::authorize('products.delete');

        if ($product->thumbnail && ! str_starts_with($product->thumbnail, 'http') && ! str_starts_with($product->thumbnail, 'mantu/')) {
            Storage::disk('public')->delete($product->thumbnail);
        }

        foreach ($product->images as $img) {
            if ($img->image && ! str_starts_with($img->image, 'http') && ! str_starts_with($img->image, 'mantu/')) {
                Storage::disk('public')->delete($img->image);
            }
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', __('Product deleted successfully.'));
    }

    /**
     * Export products to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('products.view');

        return Excel::download(new ProductsExport, 'products_'.date('Y-m-d_His').'.xlsx');
    }

    /**
     * Export products to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('products.view');

        $products = Product::query()
            ->with(['category', 'brand', 'vendor', 'size', 'color'])
            ->latest('id')
            ->get();

        return Pdf::loadView('modules.products.pdf', [
            'products' => $products,
        ])->setPaper('a4', 'landscape')->download('products_'.date('Y-m-d_His').'.pdf');
    }

    /**
     * Download product import template.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('products.create');

        return Excel::download(new ProductsTemplateExport, 'products_import_example.xlsx');
    }

    /**
     * Import products from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('products.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            Excel::import(new ProductsImport, $request->file('file'));

            return redirect()
                ->route('products.index')
                ->with('success', __('Data imported successfully.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('products.index')
                ->with('error', __('Import failed: ').$e->getMessage());
        }
    }

    /**
     * Get related master data for form dropdowns.
     *
     * @return array<string, mixed>
     */
    protected function getFormData(): array
    {
        return [
            'categories' => ProductCategory::query()->with('parent.parent')->orderBy('order')->get(),
            'brands' => ProductBrand::query()->orderBy('name')->get(),
            'vendors' => ProductVendor::query()->orderBy('name')->get(),
            'sizes' => ProductSize::query()->orderBy('id')->get(),
            'colors' => ProductColor::query()->orderBy('name')->get(),
        ];
    }

    /**
     * Parse tags from string or array.
     *
     * @param  mixed  $tags
     * @return array<string>
     */
    protected function parseTags($tags): array
    {
        if (is_array($tags)) {
            return array_values(array_filter(array_map('trim', $tags)));
        }

        if (is_string($tags) && ! empty($tags)) {
            return array_values(array_filter(array_map('trim', explode(',', $tags))));
        }

        return [];
    }

    /**
     * Parse specifications from repeater form inputs.
     *
     * @return array<int, array<string, string>>
     */
    protected function parseSpecifications(Request $request): array
    {
        $keys = $request->input('spec_keys', []);
        $values = $request->input('spec_values', []);

        $specs = [];
        if (is_array($keys) && is_array($values)) {
            foreach ($keys as $index => $key) {
                $trimmedKey = trim((string) $key);
                $trimmedVal = trim((string) ($values[$index] ?? ''));

                if ($trimmedKey !== '') {
                    $specs[] = [
                        'key' => $trimmedKey,
                        'value' => $trimmedVal,
                    ];
                }
            }
        }

        return $specs;
    }
}
