<?php

namespace App\Exports;

use App\Models\ProductColor;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductColorsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, ProductColor>
     */
    public function collection(): Collection
    {
        return ProductColor::query()->withCount('products')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Color Name',
            'Color Code (Hex)',
            'Total Products',
            'Created At',
        ];
    }

    /**
     * @param  ProductColor  $color
     */
    public function map($color): array
    {
        return [
            $color->id,
            $color->name,
            $color->color_code ?? '',
            $color->products_count,
            $color->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
