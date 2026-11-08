<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductBannersTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function collection(): Collection
    {
        return collect([
            [
                'title' => "Fashion sale for women's",
                'subtitle' => 'Elevate your every day. Style that speaks volumes.',
                'badge_label' => '50% Off',
                'button_text' => 'Shop Now',
                'button_url' => '#',
                'position' => 'hero',
                'order' => 1,
                'is_active' => '1',
            ],
            [
                'title' => "Fashion sale for Men's",
                'subtitle' => 'Wear the change. Fashion that feels good.',
                'badge_label' => '35% Off',
                'button_text' => 'Shop Now',
                'button_url' => '#',
                'position' => 'hero',
                'order' => 2,
                'is_active' => '1',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'title',
            'subtitle',
            'badge_label',
            'button_text',
            'button_url',
            'position',
            'order',
            'is_active',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
