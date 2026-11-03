<?php

namespace App\Imports;

use App\Models\SchoolClass;
use App\Models\Student;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class StudentsImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?Student
    {
        $nis = trim((string) ($row['nis'] ?? ''));
        $name = trim((string) ($row['name'] ?? $row['nama'] ?? ''));

        if ($nis === '' || $name === '') {
            return null;
        }

        $className = trim((string) ($row['class_name'] ?? $row['kelas'] ?? $row['nama_kelas'] ?? ''));
        $classId = null;

        if ($className !== '') {
            $class = SchoolClass::firstOrCreate(['class_name' => $className]);
            $classId = $class->id;
        } elseif (! empty($row['class_id'])) {
            $classId = (int) $row['class_id'];
        }

        $rawGender = strtoupper(trim((string) ($row['gender'] ?? $row['jenis_kelamin'] ?? 'L')));
        $gender = str_starts_with($rawGender, 'P') ? 'P' : 'L';

        $birthDate = null;
        $rawDate = $row['birth_date'] ?? $row['tanggal_lahir'] ?? null;
        if (! empty($rawDate)) {
            try {
                if (is_numeric($rawDate)) {
                    $birthDate = Carbon::instance(Date::excelToDateTimeObject((float) $rawDate))->format('Y-m-d');
                } else {
                    $birthDate = Carbon::parse($rawDate)->format('Y-m-d');
                }
            } catch (\Throwable) {
                $birthDate = null;
            }
        }

        return Student::updateOrCreate(
            ['nis' => $nis],
            [
                'class_id' => $classId,
                'name' => $name,
                'email' => trim((string) ($row['email'] ?? '')) ?: null,
                'phone' => trim((string) ($row['phone'] ?? $row['no_telepon'] ?? '')) ?: null,
                'gender' => $gender,
                'birth_date' => $birthDate,
                'address' => trim((string) ($row['address'] ?? $row['alamat'] ?? '')) ?: null,
            ]
        );
    }

    public function rules(): array
    {
        return [
            '*.nis' => ['nullable'],
            '*.name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
