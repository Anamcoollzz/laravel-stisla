<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, Product>
     */
    public function collection(): Collection
    {
        return Product::query()
            ->with(['category', 'brand', 'vendor', 'size', 'color'])
            ->latest('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'SKU',
            'Title',
            'Slug',
            'Category',
            'Brand',
            'Vendor',
            'Size',
            'Color',
            'Price',
            'Discount (%)',
            'Final Price',
            'Stock',
            'Tags',
            'Status',
            'Created At',
        ];
    }

    /**
     * @param  Product  $product
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->sku,
            $product->title,
            $product->slug,
            $product->category?->name ?? '-',
            $product->brand?->name ?? '-',
            $product->vendor?->name ?? '-',
            $product->size?->name ?? '-',
            $product->color?->name ?? '-',
            (float) $product->price,
            (float) $product->discount_percent,
            (float) $product->final_price,
            $product->stock,
            is_array($product->tags) ? implode(', ', $product->tags) : ($product->tags ?? ''),
            $product->is_active ? 'Active' : 'Inactive',
            $product->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

