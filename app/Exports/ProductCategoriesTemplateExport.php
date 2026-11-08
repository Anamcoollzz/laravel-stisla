<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductCategoriesTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function collection(): Collection
    {
        return collect([
            [
                'name' => 'Gadgets & Devices',
                'parent_name_or_id' => '',
                'slug' => 'gadgets-and-devices',
                'icon' => 'mantu/assets/img/icons/clothes-2.svg',
                'route' => '',
                'url' => '#',
                'order' => 1,
                'is_active' => 1,
                'description' => 'Level 1: Section Header (leave parent empty)',
            ],
            [
                'name' => 'Smartphones',
                'parent_name_or_id' => 'Gadgets & Devices',
                'slug' => 'smartphones',
                'icon' => 'mantu/assets/img/icons/clothes-2.svg',
                'route' => 'ecommerce.shop.html',
                'url' => '#',
                'order' => 1,
                'is_active' => 1,
                'description' => 'Level 2: Main Category (parent is Section)',
            ],
            [
                'name' => 'Android Phones',
                'parent_name_or_id' => 'Smartphones',
                'slug' => 'android-phones',
                'icon' => '',
                'route' => 'ecommerce.shop-right-sidebar.html',
                'url' => '',
                'order' => 1,
                'is_active' => 1,
                'description' => 'Level 3: Subcategory Item (parent is Category)',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'name',
            'parent_name_or_id',
            'slug',
            'icon',
            'route',
            'url',
            'order',
            'is_active',
            'description',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

