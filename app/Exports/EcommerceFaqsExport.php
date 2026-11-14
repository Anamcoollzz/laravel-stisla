<?php

namespace App\Exports;

use App\Models\EcommerceFaq;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EcommerceFaqsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, EcommerceFaq>
     */
    public function collection(): Collection
    {
        return EcommerceFaq::query()->orderBy('column')->orderBy('order')->latest('id')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Question',
            'Answer',
            'Column',
            'Order',
            'Active',
            'Created At',
        ];
    }

    /**
     * @param  EcommerceFaq  $faq
     */
    public function map($faq): array
    {
        return [
            $faq->id,
            $faq->question,
            $faq->answer,
            $faq->column,
            $faq->order,
            $faq->is_active ? 'Yes' : 'No',
            $faq->created_at?->format('Y-m-d H:i:s'),
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
