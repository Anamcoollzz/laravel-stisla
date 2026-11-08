<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductSizesTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return Collection<int, array<string, string>>
     */
    public function collection(): Collection
    {
        return collect([
            ['name' => 'S', 'code' => 'S'],
            ['name' => 'M', 'code' => 'M'],
            ['name' => 'L', 'code' => 'L'],
            ['name' => 'XL', 'code' => 'XL'],
            ['name' => 'All Size', 'code' => 'ALL'],
        ]);
    }

    public function headings(): array
    {
        return [
            'name',
            'code',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
