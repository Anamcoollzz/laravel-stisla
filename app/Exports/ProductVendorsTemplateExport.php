<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductVendorsTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * @return Collection<int, array<string, string>>
     */
    public function collection(): Collection
    {
        return collect([
            [
                'name' => 'PT Mitra Busana Nusantara',
                'phone' => '+62 812-3456-7890',
                'email' => 'sales@mitrabusana.co.id',
                'address' => 'Jl. Jend. Sudirman Kav. 45, Jakarta Selatan',
                'description' => 'Supplier resmi fashion garment dan tekstil premium',
            ],
            [
                'name' => 'CV Sentosa Jaya Abadi',
                'phone' => '+62 823-4567-8901',
                'email' => 'contact@sentosajaya.com',
                'address' => 'Jl. Riau No. 120, Bandung',
                'description' => 'Distributor gadget dan aksesoris',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'name',
            'phone',
            'email',
            'address',
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
