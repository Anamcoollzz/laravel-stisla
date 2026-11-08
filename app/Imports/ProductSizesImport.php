<?php

namespace App\Imports;

use App\Models\ProductSize;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductSizesImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?ProductSize
    {
        $name = trim((string) ($row['name'] ?? $row['nama_size'] ?? $row['ukuran'] ?? ''));

        if ($name === '') {
            return null;
        }

        $code = trim((string) ($row['code'] ?? $row['kode'] ?? ''));

        return ProductSize::firstOrCreate(
            ['name' => $name],
            ['code' => $code ?: strtoupper($name)]
        );
    }

    public function rules(): array
    {
        return [
            '*.name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
