<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkersTemplateExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        return [
            [
                'PKA-2026-099',
                'siti-rahmawati-sample',
                'Siti Rahmawati',
                'ART',
                'Domestic Helper (ART)',
                28,
                '26-35',
                'Banyumas, Central Java',
                'Central Java',
                'Senior High School (SMA)',
                4.0,
                '4 Years',
                '3-5',
                'Available',
                1,
                '2026-11-15',
                'lp-assets/images/workers/worker-1.jpg',
                'Indonesian (Native) | Javanese',
                'Organized and dependable domestic helper.',
                'Deep Cleaning & Organization | Daily Indonesian Cooking | Laundry & Steam Ironing',
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'code',
            'slug',
            'name',
            'category',
            'category_label',
            'age',
            'age_bracket',
            'origin',
            'origin_region',
            'education',
            'experience_years',
            'experience_label',
            'experience_bracket',
            'status',
            'is_featured',
            'published_date',
            'photo',
            'languages',
            'bio',
            'skills',
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
