<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeachersTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return Collection<int, array<string, string>>
     */
    public function collection(): Collection
    {
        return collect([
            [
                'nip' => '198501012010011001',
                'name' => 'Budi Santoso, M.Pd',
                'email' => 'budi.santoso@sekolah.sch.id',
                'phone' => '081234567890',
                'gender' => 'L',
                'address' => 'Jl. Pahlawan No. 10, Jakarta',
            ],
            [
                'nip' => '199002022015022002',
                'name' => 'Dewi Lestari, S.Pd',
                'email' => 'dewi.lestari@sekolah.sch.id',
                'phone' => '081234567891',
                'gender' => 'P',
                'address' => 'Jl. Melati No. 5, Bandung',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'nip',
            'name',
            'email',
            'phone',
            'gender',
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
