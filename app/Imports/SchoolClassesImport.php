<?php

namespace App\Imports;

use App\Models\SchoolClass;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SchoolClassesImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?SchoolClass
    {
        $className = trim((string) ($row['class_name'] ?? $row['nama_kelas'] ?? ''));

        if ($className === '') {
            return null;
        }

        return SchoolClass::firstOrCreate([
            'class_name' => $className,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.class_name' => ['nullable', 'string', 'max:255'],
            '*.nama_kelas' => ['nullable', 'string', 'max:255'],
        ];
    }
}
