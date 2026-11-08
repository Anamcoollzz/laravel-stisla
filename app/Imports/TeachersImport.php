<?php

namespace App\Imports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TeachersImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?Teacher
    {
        $nip = trim((string) ($row['nip'] ?? ''));
        $name = trim((string) ($row['name'] ?? $row['nama'] ?? ''));
        $email = trim((string) ($row['email'] ?? ''));

        if ($nip === '' || $name === '' || $email === '') {
            return null;
        }

        $rawGender = strtoupper(trim((string) ($row['gender'] ?? $row['jenis_kelamin'] ?? 'L')));
        $gender = str_starts_with($rawGender, 'P') ? 'P' : 'L';

        return Teacher::updateOrCreate(
            ['nip' => $nip],
            [
                'name' => $name,
                'email' => $email,
                'phone' => trim((string) ($row['phone'] ?? $row['no_telepon'] ?? $row['telepon'] ?? '')) ?: null,
                'gender' => $gender,
                'address' => trim((string) ($row['address'] ?? $row['alamat'] ?? '')) ?: null,
            ]
        );
    }

    public function rules(): array
    {
        return [
            '*.nip' => ['nullable'],
            '*.name' => ['nullable', 'string', 'max:255'],
            '*.email' => ['nullable', 'string', 'max:255'],
        ];
    }
}
