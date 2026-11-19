<?php

namespace App\Exports;

use App\Models\LP\RecruitmentStep;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RecruitmentStepsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, RecruitmentStep>
     */
    public function collection(): Collection
    {
        return RecruitmentStep::query()->orderBy('order')->orderBy('id')->get();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'ID',
            'Step',
            'Title',
            'Description',
            'Icon',
            'Order',
            'Status',
            'Created At',
        ];
    }

    /**
     * @param  RecruitmentStep  $item
     * @return array<int, mixed>
     */
    public function map($item): array
    {
        return [
            $item->id,
            $item->step,
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
