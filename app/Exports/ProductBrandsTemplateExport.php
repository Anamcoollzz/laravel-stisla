<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductBrandsTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return Collection<int, array<string, string>>
     */
    public function collection(): Collection
    {
        return collect([
            ['name' => 'Nike', 'slug' => 'nike'],
            ['name' => 'Adidas', 'slug' => 'adidas'],
            ['name' => 'Zara', 'slug' => 'zara'],
        ]);
    }

    public function headings(): array
    {
        return [
            'name',
            'slug',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
