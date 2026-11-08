<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function collection(): Collection
    {
        return collect([
            [
                'title' => 'Oversized Streetwear Fleece Hoodie',
                'sku' => 'HOD-2026-0001',
                'price' => 299000,
                'discount_percent' => 10,
                'stock' => 50,
                'category_name_or_id' => 'Clothes',
                'brand_name_or_id' => 'Nike',
                'vendor_name_or_id' => 'PT Mitra Busana Nusantara',
                'size_name_or_id' => 'L',
                'color_name_or_id' => 'Black',
                'short_description' => 'Hoodie bahan fleece premium 330gsm, sangat hangat dan modis.',
                'tags' => 'hoodie, streetwear, winter, cotton',
                'is_active' => 1,
                'is_trending' => 1,
                'is_new' => 1,
                'is_sale' => 0,
            ],
            [
                'title' => 'Wireless Bluetooth Noise Cancelling Headphones',
                'sku' => 'AUD-2026-0002',
                'price' => 1250000,
                'discount_percent' => 15,
                'stock' => 30,
                'category_name_or_id' => 'Fashion',
                'brand_name_or_id' => 'Sony',
                'vendor_name_or_id' => 'CV Sentosa Jaya Abadi',
                'size_name_or_id' => 'All Size',
                'color_name_or_id' => 'Navy Blue',
                'short_description' => 'Headphone nirkabel dengan daya tahan baterai hingga 30 jam.',
                'tags' => 'audio, headphone, wireless, bluetooth',
                'is_active' => 1,
                'is_trending' => 0,
                'is_new' => 1,
                'is_sale' => 1,
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'title',
            'sku',
            'price',
            'discount_percent',
            'stock',
            'category_name_or_id',
            'brand_name_or_id',
            'vendor_name_or_id',
            'size_name_or_id',
            'color_name_or_id',
            'short_description',
            'tags',
            'is_active',
            'is_trending',
            'is_new',
            'is_sale',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
