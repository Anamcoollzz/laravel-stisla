<?php

namespace App\Exports;

use App\Models\LP\Worker;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkersExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, Worker>
     */
    public function collection(): Collection
    {
        return Worker::query()->latest('id')->get();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'ID',
            'Code',
            'Slug',
            'Name',
            'Category',
            'Category Label',
            'Age',
            'Origin',
            'Origin Region',
            'Education',
            'Experience Years',
            'Status',
            'Featured',
            'Published Date',
            'Skills',
            'Languages',
        ];
    }

    /**
     * @param  Worker  $worker
     * @return array<int, mixed>
     */
    public function map($worker): array
    {
        return [
            $worker->id,
            $worker->code,
            $worker->slug,
            $worker->name,
            $worker->category,
            $worker->category_label,
            $worker->age,
            $worker->origin,
            $worker->origin_region,
            $worker->education,
            $worker->experience_years,
            $worker->status,
            $worker->is_featured ? 'Yes' : 'No',
            $worker->published_date?->format('Y-m-d'),
            implode(' | ', $worker->skills ?? []),
            implode(' | ', $worker->languages ?? []),
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
