<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NewsTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function collection(): Collection
    {
        return collect([
            [
                'title' => 'Contoh Judul Berita Teknologi',
                'slug' => 'contoh-judul-berita-teknologi',
                'category' => 'Teknologi',
                'content' => 'Isi konten berita lengkap dapat ditulis di kolom ini.',
                'status' => 'published',
                'published_at' => now()->format('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Contoh Berita Draft Bisnis',
                'slug' => '',
                'category' => 'Bisnis & Ekonomi',
                'content' => 'Jika slug dikosongkan, sistem akan membuat slug otomatis dari judul.',
                'status' => 'draft',
                'published_at' => '',
            ],
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'title',
            'slug',
            'category',
            'content',
            'status',
            'published_at',
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
