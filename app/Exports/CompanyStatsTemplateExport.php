<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompanyStatsTemplateExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        return [
            [
                '10+',
                'Years of Experience',
                'Sample benchmark representing dedicated domestic & care recruitment experience.',
                'award',
                1,
                1,
            ],
            [
                '850+',
                'Workers Available',
                'Pre-screened candidates across childcare, eldercare, and household support.',
                'users',
                2,
                1,
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'value',
            'label',
            'description',
            'icon',
            'order',
            'is_active',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '6777EF'],
                ],
            ],
        ];
    }
}
