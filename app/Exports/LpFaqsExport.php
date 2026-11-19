<?php

namespace App\Exports;

use App\Models\LP\LpFaq;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LpFaqsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, LpFaq>
     */
    public function collection(): Collection
    {
        return LpFaq::query()->orderBy('order')->orderBy('id')->get();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'ID',
            'Question',
            'Answer',
            'Order',
            'Status',
            'Created At',
        ];
    }

    /**
     * @param  LpFaq  $item
     * @return array<int, mixed>
     */
    public function map($item): array
    {
        return [
            $item->id,
            $item->question,
            $item->answer,
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
