<?php

namespace App\Exports;

use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SchoolClassesExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, SchoolClass>
     */
    public function collection(): Collection
    {
        return SchoolClass::query()->latest('id')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Kelas',
            'Dibuat Pada',
            'Diperbarui Pada',
        ];
    }

    /**
     * @param  SchoolClass  $schoolClass
     */
    public function map($schoolClass): array
    {
        return [
            $schoolClass->id,
            $schoolClass->class_name,
            $schoolClass->created_at?->format('Y-m-d H:i:s'),
            $schoolClass->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
