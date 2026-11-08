<?php

namespace App\Exports;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeachersExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, Teacher>
     */
    public function collection(): Collection
    {
        return Teacher::query()->latest('id')->get();
    }

    public function headings(): array
    {
        return [
            'NIP',
            'Nama Guru',
            'Email',
            'No Telepon',
            'Jenis Kelamin',
            'Alamat',
            'Dibuat Pada',
        ];
    }

    /**
     * @param  Teacher  $teacher
     */
    public function map($teacher): array
    {
        return [
            $teacher->nip,
            $teacher->name,
            $teacher->email,
            $teacher->phone ?? '-',
            $teacher->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            $teacher->address ?? '-',
            $teacher->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
