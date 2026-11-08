<?php

namespace App\Exports;

use App\Models\ProductBanner;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductBannersExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, ProductBanner>
     */
    public function collection(): Collection
    {
        return ProductBanner::query()->orderBy('order')->latest('id')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Subtitle',
            'Badge Label',
            'Button Text',
            'Button URL',
            'Position',
            'Order',
            'Active',
            'Created At',
        ];
    }

    /**
     * @param  ProductBanner  $banner
     */
    public function map($banner): array
    {
        return [
            $banner->id,
            $banner->title,
            $banner->subtitle,
            $banner->badge_label,
            $banner->button_text,
            $banner->button_url,
            $banner->position,
            $banner->order,
            $banner->is_active ? 'Yes' : 'No',
            $banner->created_at?->format('Y-m-d H:i:s'),
        ];
    }

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
