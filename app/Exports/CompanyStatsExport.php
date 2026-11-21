<?php

namespace App\Exports;

use App\Models\LP\CompanyStat;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompanyStatsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, CompanyStat>
     */
    public function collection(): Collection
    {
        return CompanyStat::query()->orderBy('order')->orderBy('id')->get();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'ID',
            'Value',
            'Label',
            'Description',
            'Icon',
            'Order',
            'Status',
            'Created At',
        ];
    }

    /**
     * @param  CompanyStat  $stat
     * @return array<int, mixed>
     */
    public function map($stat): array
    {
        return [
            $stat->id,
            $stat->value,
            $stat->label,
            $stat->description,
            $stat->icon,
            $stat->order,
            $stat->is_active ? 'Active' : 'Inactive',
            $stat->created_at?->format('Y-m-d H:i:s'),
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
