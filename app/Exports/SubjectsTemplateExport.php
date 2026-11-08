<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubjectsTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return Collection<int, array<string, string>>
     */
    public function collection(): Collection
    {
        return collect([
            [
                'code' => 'MTK-10',
                'name' => 'Matematika Wajib',
                'description' => 'Matematika dasar untuk kelas 10 SMA',
            ],
            [
                'code' => 'BIN-10',
                'name' => 'Bahasa Indonesia',
                'description' => 'Bahasa dan sastra Indonesia tingkat dasar',
            ],
            [
                'code' => 'BIG-10',
                'name' => 'Bahasa Inggris',
                'description' => 'General English and comprehension',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'code',
            'name',
            'description',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
