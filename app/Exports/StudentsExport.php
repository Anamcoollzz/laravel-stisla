<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return Collection<int, Student>
     */
    public function collection(): Collection
    {
        return Student::query()->with('schoolClass')->latest('id')->get();
    }

    public function headings(): array
    {
        return [
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Email',
            'No Telepon',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Alamat',
            'Dibuat Pada',
        ];
    }

    /**
     * @param  Student  $student
     */
    public function map($student): array
    {
        return [
            $student->nis,
            $student->name,
            $student->schoolClass?->class_name ?? '-',
            $student->email ?? '-',
            $student->phone ?? '-',
            $student->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            $student->birth_date?->format('Y-m-d') ?? '-',
            $student->address ?? '-',
            $student->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
