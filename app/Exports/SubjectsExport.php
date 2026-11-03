<?php

namespace App\Exports;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubjectsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, Subject>
     */
    public function collection(): Collection
    {
        return Subject::query()->latest('id')->get();
    }

    public function headings(): array
    {
        return [
            'Kode Mapel',
            'Nama Mata Pelajaran',
            'Deskripsi',
            'Dibuat Pada',
        ];
    }

    /**
     * @param  Subject  $subject
     */
    public function map($subject): array
    {
        return [
            $subject->code,
            $subject->name,
            $subject->description ?? '-',
            $subject->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
