<?php

namespace App\Imports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SubjectsImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?Subject
    {
        $code = trim((string) ($row['code'] ?? $row['kode'] ?? $row['kode_mapel'] ?? ''));
        $name = trim((string) ($row['name'] ?? $row['nama'] ?? $row['nama_mapel'] ?? ''));

        if ($code === '' || $name === '') {
            return null;
        }

        return Subject::updateOrCreate(
            ['code' => $code],
            [
                'name' => $name,
                'description' => trim((string) ($row['description'] ?? $row['deskripsi'] ?? '')) ?: null,
            ]
        );
    }

    public function rules(): array
    {
        return [
            '*.code' => ['nullable'],
            '*.name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
