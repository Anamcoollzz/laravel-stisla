<?php

namespace App\Exports;

use App\Models\LP\WhyChooseUs;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WhyChooseUsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, WhyChooseUs>
     */
    public function collection(): Collection
    {
        return WhyChooseUs::query()->orderBy('order')->orderBy('id')->get();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Description',
            'Icon',
            'Order',
            'Status',
            'Created At',
        ];
    }

    /**
     * @param  WhyChooseUs  $item
     * @return array<int, mixed>
     */
    public function map($item): array
    {
        return [
            $item->id,
            $item->title,
            $item->description,
            $item->icon,
            $item->order,
            $item->is_active ? 'Active' : 'Inactive',
            $item->created_at?->format('Y-m-d H:i:s'),
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
