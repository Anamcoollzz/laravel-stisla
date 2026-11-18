<?php

namespace App\Exports;

use App\Models\News\NewsComment;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NewsCommentsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, NewsComment>
     */
    public function collection(): Collection
    {
        return NewsComment::query()->with(['news', 'parent', 'user'])->latest('id')->get();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'ID',
            'News Title',
            'Name',
            'Email',
            'Website',
            'Comment',
            'Reply To ID',
            'Verified',
            'Created At',
        ];
    }

    /**
     * @param  NewsComment  $comment
     * @return array<int, mixed>
     */
    public function map($comment): array
    {
        return [
            $comment->id,
            $comment->news?->title ?? '-',
            $comment->name,
            $comment->email,
            $comment->website ?? '-',
            $comment->comment,
            $comment->parent_id ?? '-',
            $comment->is_approved ? 'Verified' : 'Pending',
            $comment->created_at?->format('Y-m-d H:i:s'),
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
