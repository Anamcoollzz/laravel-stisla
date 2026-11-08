<?php

namespace App\Imports;

use App\Models\ProductColor;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductColorsImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?ProductColor
    {
        $name = trim((string) ($row['name'] ?? $row['nama_warna'] ?? $row['warna'] ?? ''));

        if ($name === '') {
            return null;
        }

        $colorCode = trim((string) ($row['color_code'] ?? $row['kode_warna'] ?? $row['hex'] ?? ''));

        return ProductColor::firstOrCreate(
            ['name' => $name],
            ['color_code' => $colorCode ?: null]
        );
    }

    public function rules(): array
    {
        return [
            '*.name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
