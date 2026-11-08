<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductVendor;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?Product
    {
        $title = trim((string) ($row['title'] ?? $row['nama_produk'] ?? ''));

        if ($title === '') {
            return null;
        }

        // 1. Resolve Category
        $catInput = trim((string) ($row['category_name_or_id'] ?? $row['category_id'] ?? $row['category'] ?? ''));
        $categoryId = null;
        if ($catInput !== '') {
            if (is_numeric($catInput)) {
                $categoryId = ProductCategory::find((int) $catInput)?->id;
            } else {
                $categoryId = ProductCategory::where('name', $catInput)->first()?->id
                    ?? ProductCategory::where('slug', $catInput)->first()?->id;
            }
        }

        // 2. Resolve Brand
        $brandInput = trim((string) ($row['brand_name_or_id'] ?? $row['brand_id'] ?? $row['brand'] ?? ''));
        $brandId = null;
        if ($brandInput !== '') {
            if (is_numeric($brandInput)) {
                $brandId = ProductBrand::find((int) $brandInput)?->id;
            } else {
                $brandId = ProductBrand::firstOrCreate(['name' => $brandInput])->id;
            }
        }

        // 3. Resolve Vendor
        $vendorInput = trim((string) ($row['vendor_name_or_id'] ?? $row['vendor_id'] ?? $row['vendor'] ?? ''));
        $vendorId = null;
        if ($vendorInput !== '') {
            if (is_numeric($vendorInput)) {
                $vendorId = ProductVendor::find((int) $vendorInput)?->id;
            } else {
                $vendorId = ProductVendor::where('name', $vendorInput)->first()?->id;
            }
        }

        // 4. Resolve Size
        $sizeInput = trim((string) ($row['size_name_or_id'] ?? $row['size_id'] ?? $row['size'] ?? ''));
        $sizeId = null;
        if ($sizeInput !== '') {
            if (is_numeric($sizeInput)) {
                $sizeId = ProductSize::find((int) $sizeInput)?->id;
            } else {
                $sizeId = ProductSize::firstOrCreate(['name' => $sizeInput], ['code' => strtoupper($sizeInput)])->id;
            }
        }

        // 5. Resolve Color
        $colorInput = trim((string) ($row['color_name_or_id'] ?? $row['color_id'] ?? $row['color'] ?? ''));
        $colorId = null;
        if ($colorInput !== '') {
            if (is_numeric($colorInput)) {
                $colorId = ProductColor::find((int) $colorInput)?->id;
            } else {
                $colorId = ProductColor::firstOrCreate(['name' => $colorInput])->id;
            }
        }

        // SKU
        $sku = trim((string) ($row['sku'] ?? ''));
        if ($sku === '') {
            $sku = Product::generateUniqueSku();
        }

        // Tags parsing
        $tagsRaw = $row['tags'] ?? [];
        $tags = [];
        if (is_string($tagsRaw)) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $tagsRaw))));
        } elseif (is_array($tagsRaw)) {
            $tags = $tagsRaw;
        }

        // Active flag
        $activeRaw = $row['is_active'] ?? $row['status'] ?? 1;
        $isActive = in_array(strtolower(trim((string) $activeRaw)), ['1', 'true', 'yes', 'aktif', 'active'], true);
        $isTrending = in_array(strtolower(trim((string) ($row['is_trending'] ?? $row['trending'] ?? '0'))), ['1', 'true', 'yes', 'aktif', 'active'], true);
        $isNew = in_array(strtolower(trim((string) ($row['is_new'] ?? $row['new'] ?? '0'))), ['1', 'true', 'yes', 'aktif', 'active'], true);
        $isSale = in_array(strtolower(trim((string) ($row['is_sale'] ?? $row['sale'] ?? '0'))), ['1', 'true', 'yes', 'aktif', 'active'], true);

        // Prices & stock
        $price = isset($row['price']) && is_numeric($row['price']) ? (float) $row['price'] : 0.00;
        $discountPercent = isset($row['discount_percent']) && is_numeric($row['discount_percent']) ? (float) $row['discount_percent'] : 0.00;
        $stock = isset($row['stock']) && is_numeric($row['stock']) ? (int) $row['stock'] : 0;

        $slug = Product::generateUniqueSlug($title);

        return Product::updateOrCreate(
            ['sku' => $sku],
            [
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'vendor_id' => $vendorId,
                'size_id' => $sizeId,
                'color_id' => $colorId,
                'title' => $title,
                'slug' => $slug,
                'price' => $price,
                'discount_percent' => $discountPercent,
                'stock' => $stock,
                'short_description' => $row['short_description'] ?? null,
                'description_detail' => $row['description_detail'] ?? null,
                'tags' => $tags,
                'is_active' => $isActive,
                'is_trending' => $isTrending,
                'is_new' => $isNew,
                'is_sale' => $isSale,
            ]
        );
    }

    public function rules(): array
    {
        return [
            '*.title' => ['nullable', 'string', 'max:255'],
            '*.nama_produk' => ['nullable', 'string', 'max:255'],
        ];
    }
}
