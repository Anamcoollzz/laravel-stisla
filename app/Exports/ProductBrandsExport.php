<?php

namespace App\Exports;

use App\Models\ProductBrand;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductBrandsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, ProductBrand>
     */
    public function collection(): Collection
    {
        return ProductBrand::query()->withCount('products')->latest('id')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Brand Name',
            'Slug',
            'Total Products',
            'Created At',
        ];
    }

    /**
     * @param  ProductBrand  $brand
     */
    public function map($brand): array
    {
        return [
            $brand->id,
            $brand->name,
            $brand->slug,
            $brand->products_count,
            $brand->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
