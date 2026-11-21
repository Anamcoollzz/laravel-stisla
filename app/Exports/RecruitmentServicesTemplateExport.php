<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RecruitmentServicesTemplateExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        return [
            [
                'domestic-helper',
                'ART',
                'Domestic Helper (ART)',
                'Household Upkeep & Culinary Support',
                'home',
                'lp-assets/images/services/art.jpg',
                'Dependable domestic helpers trained in daily housekeeping, laundry care, and healthy family meal preparation.',
                'Our Domestic Helpers are trained in structured residential cleaning, appliance operation, fabric care, and hygienic daily cooking.',
                'Daily sweeping and mopping | Laundry sorting and ironing | Preparing daily meals',
                'Residential Housekeeping | Home Cooking | Modern Appliances',
                'Verified identity checks | Structured orientation | Dedicated consultant follow-up',
                1,
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
            'slug',
            'category_key',
            'title',
            'subtitle',
            'icon',
            'image',
            'short_description',
            'description',
            'responsibilities',
            'skills',
            'benefits',
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
