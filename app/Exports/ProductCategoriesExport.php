<?php

namespace App\Exports;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductCategoriesExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, ProductCategory>
     */
    public function collection(): Collection
    {
        return ProductCategory::query()
            ->with(['parent.parent'])
            ->get()
            ->sortBy(function (ProductCategory $c) {
                $p1 = $c->parent?->parent_id ? $c->parent->parent_id : ($c->parent_id ?: $c->id);
                $p2 = $c->parent?->parent_id ? $c->parent_id : ($c->parent_id ? $c->id : 0);
                $p3 = $c->parent?->parent_id ? $c->id : 0;

                return sprintf('%05d-%05d-%05d-%05d', $p1, $p2, $p3, $c->order);
            })
            ->values();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Parent Category',
            'Level',
            'Name',
            'Slug',
            'Icon',
            'Route',
            'URL',
            'Order',
            'Status',
            'Description',
            'Created At',
        ];
    }

    /**
     * @param  ProductCategory  $category
     */
    public function map($category): array
    {
        return [
            $category->id,
            $category->parent?->name ?? '',
            'Level '.$category->level,
            $category->name,
            $category->slug,
            $category->icon ?? '',
            $category->route ?? '',
            $category->url ?? '',
            $category->order,
            $category->is_active ? 'Active' : 'Inactive',
            $category->description ?? '',
            $category->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

