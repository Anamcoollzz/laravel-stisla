<?php

namespace App\Exports;

use App\Models\LP\RecruitmentService;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RecruitmentServicesExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, RecruitmentService>
     */
    public function collection(): Collection
    {
        return RecruitmentService::query()->orderBy('order')->orderBy('id')->get();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'ID',
            'Slug',
            'Category Key',
            'Title',
            'Subtitle',
            'Icon',
            'Image',
            'Short Description',
            'Description',
            'Responsibilities',
            'Skills',
            'Benefits',
            'Order',
            'Status',
            'Created At',
        ];
    }

    /**
     * @param  RecruitmentService  $service
     * @return array<int, mixed>
     */
    public function map($service): array
    {
        return [
            $service->id,
            $service->slug,
            $service->category_key,
            $service->title,
            $service->subtitle,
            $service->icon,
            $service->image,
            $service->short_description,
            $service->description,
            implode(' | ', $service->responsibilities ?? []),
            implode(' | ', $service->skills ?? []),
            implode(' | ', $service->benefits ?? []),
            $service->order,
            $service->is_active ? 'Active' : 'Inactive',
            $service->created_at?->format('Y-m-d H:i:s'),
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
