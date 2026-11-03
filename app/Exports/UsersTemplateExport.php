<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return Collection<int, array<string, string>>
     */
    public function collection(): Collection
    {
        return collect([
            [
                'name' => 'Operator Sekolah',
                'email' => 'operator1@sekolah.sch.id',
                'password' => 'password123',
                'role' => 'staff',
            ],
            [
                'name' => 'Staf Tata Usaha',
                'email' => 'tu@sekolah.sch.id',
                'password' => 'password123',
                'role' => 'staff',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'name',
            'email',
            'password',
            'role',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
