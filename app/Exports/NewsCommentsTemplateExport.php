<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NewsCommentsTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function collection(): Collection
    {
        return collect([
            [
                'news_slug' => 'transformasi-digital-umkm-indonesia-makin-cepat-di-tahun-2026',
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'website' => 'https://example.com',
                'comment' => 'Artikel yang sangat bermanfaat!',
                'is_approved' => '1',
            ],
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'news_slug',
            'name',
            'email',
            'website',
            'comment',
            'is_approved',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
