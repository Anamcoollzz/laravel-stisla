<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EcommerceFaqsTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function collection(): Collection
    {
        return collect([
            [
                'question' => 'What is the multi vendor services?',
                'answer' => 'Our platform supports multiple verified vendors offering curated products with unified checkout.',
                'column' => 'left',
                'order' => 1,
                'is_active' => '1',
            ],
            [
                'question' => 'How long does shipping take?',
                'answer' => 'Standard shipping typically takes 3-5 business days depending on your destination.',
                'column' => 'right',
                'order' => 1,
                'is_active' => '1',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'question',
            'answer',
            'column',
            'order',
            'is_active',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
