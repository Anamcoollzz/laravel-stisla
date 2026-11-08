<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SppPaymentsTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function collection(): Collection
    {
        return collect([
            [
                'student_nis' => '2026000001',
                'month' => 1,
                'year' => 2026,
                'amount' => 250000,
                'payment_date' => '2026-01-10',
                'payment_method' => 'transfer',
                'status' => 'paid',
                'notes' => 'Lunas via transfer BCA',
            ],
            [
                'student_nis' => '2026000002',
                'month' => 1,
                'year' => 2026,
                'amount' => 250000,
                'payment_date' => '2026-01-12',
                'payment_method' => 'cash',
                'status' => 'paid',
                'notes' => 'Bayar tunai di tata usaha',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'student_nis',
            'month',
            'year',
            'amount',
            'payment_date',
            'payment_method',
            'status',
            'notes',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
