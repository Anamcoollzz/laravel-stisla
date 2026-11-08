<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return Collection<int, array<string, string>>
     */
    public function collection(): Collection
    {
        return collect([
            [
                'nis' => '2026000001',
                'name' => 'Ahmad Fauzi',
                'class_name' => 'Kelas 10-A',
                'email' => 'ahmad.fauzi@student.sch.id',
                'phone' => '081398765432',
                'gender' => 'L',
                'birth_date' => '2008-05-14',
                'address' => 'Jl. Mawar No. 12, Jakarta',
            ],
            [
                'nis' => '2026000002',
                'name' => 'Siti Nurhaliza',
                'class_name' => 'Kelas 10-B',
                'email' => 'siti.nur@student.sch.id',
                'phone' => '081398765433',
                'gender' => 'P',
                'birth_date' => '2008-08-21',
                'address' => 'Jl. Anggrek No. 8, Tangerang',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'nis',
            'name',
            'class_name',
            'email',
            'phone',
            'gender',
            'birth_date',
            'address',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
