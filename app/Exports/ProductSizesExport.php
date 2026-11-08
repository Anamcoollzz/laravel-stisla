<?php

namespace App\Exports;

use App\Models\ProductSize;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductSizesExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, ProductSize>
     */
    public function collection(): Collection
    {
        return ProductSize::query()->withCount('products')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Size Name',
            'Code',
            'Total Products',
            'Created At',
        ];
    }

    /**
     * @param  ProductSize  $size
     */
    public function map($size): array
    {
        return [
            $size->id,
            $size->name,
            $size->code ?? '',
            $size->products_count,
            $size->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
