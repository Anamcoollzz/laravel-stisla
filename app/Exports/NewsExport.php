<?php

namespace App\Exports;

use App\Models\News\News;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NewsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, News>
     */
    public function collection(): Collection
    {
        return News::query()->with(['category', 'publisher'])->latest('id')->get();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Slug',
            'Category',
            'Publisher',
            'Status',
            'Published At',
            'Content',
            'Created At',
        ];
    }

    /**
     * @param  News  $news
     * @return array<int, mixed>
     */
    public function map($news): array
    {
        return [
            $news->id,
            $news->title,
            $news->slug,
            $news->category?->name ?? '-',
            $news->publisher?->name ?? '-',
            $news->status,
            $news->published_at?->format('Y-m-d H:i:s'),
            $news->content,
            $news->created_at?->format('Y-m-d H:i:s'),
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
