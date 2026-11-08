<?php

namespace App\Imports;

use App\Models\ProductCategory;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductCategoriesImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?ProductCategory
    {
        $name = trim((string) ($row['name'] ?? $row['nama_kategori'] ?? ''));

        if ($name === '') {
            return null;
        }

        // Resolve Parent
        $parentInput = trim((string) ($row['parent_name_or_id'] ?? $row['parent_id'] ?? $row['parent'] ?? ''));
        $parentId = null;

        if ($parentInput !== '') {
            if (is_numeric($parentInput)) {
                $parentCat = ProductCategory::find((int) $parentInput);
            } else {
                $parentCat = ProductCategory::where('name', $parentInput)->first()
                    ?? ProductCategory::where('slug', $parentInput)->first();
            }

            if ($parentCat) {
                // Ensure max 3 levels: if parent is already Level 3, attach to its parent
                if ($parentCat->level >= 3) {
                    $parentId = $parentCat->parent_id;
                } else {
                    $parentId = $parentCat->id;
                }
            }
        }

        // Active flag
        $activeRaw = $row['is_active'] ?? $row['status'] ?? 1;
        $isActive = in_array(strtolower(trim((string) $activeRaw)), ['1', 'true', 'yes', 'aktif', 'active'], true);

        // Order
        $order = isset($row['order']) && is_numeric($row['order']) ? (int) $row['order'] : 0;

        // Existing by ID check
        $id = isset($row['id']) && is_numeric($row['id']) ? (int) $row['id'] : null;
        if ($id && ($existing = ProductCategory::find($id))) {
            $existing->update([
                'parent_id' => $parentId,
                'name' => $name,
                'slug' => ! empty($row['slug']) ? ProductCategory::generateUniqueSlug($row['slug'], $existing->id) : $existing->slug,
                'icon' => ! empty($row['icon']) ? $row['icon'] : $existing->icon,
                'route' => $row['route'] ?? null,
                'url' => $row['url'] ?? null,
                'order' => $order,
                'is_active' => $isActive,
                'description' => $row['description'] ?? null,
            ]);

            return $existing;
        }

        // Slug resolution
        $slug = ! empty($row['slug'])
            ? ProductCategory::generateUniqueSlug($row['slug'])
            : ProductCategory::generateUniqueSlug($name);

        return ProductCategory::updateOrCreate(
            ['slug' => $slug],
            [
                'parent_id' => $parentId,
                'name' => $name,
                'icon' => $row['icon'] ?? null,
                'route' => $row['route'] ?? null,
                'url' => $row['url'] ?? null,
                'order' => $order,
                'is_active' => $isActive,
                'description' => $row['description'] ?? null,
            ]
        );
    }

    public function rules(): array
    {
        return [
            '*.name' => ['nullable', 'string', 'max:255'],
            '*.nama_kategori' => ['nullable', 'string', 'max:255'],
        ];
    }
}

