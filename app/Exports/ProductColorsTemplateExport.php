<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductColorsTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return Collection<int, array<string, string>>
     */
    public function collection(): Collection
    {
        return collect([
            ['name' => 'Black', 'color_code' => '#000000'],
            ['name' => 'White', 'color_code' => '#FFFFFF'],
            ['name' => 'Navy Blue', 'color_code' => '#001F3F'],
            ['name' => 'Crimson Red', 'color_code' => '#DC143C'],
        ]);
    }

    public function headings(): array
    {
        return [
            'name',
            'color_code',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
