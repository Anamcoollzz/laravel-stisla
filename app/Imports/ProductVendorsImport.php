<?php

namespace App\Imports;

use App\Models\ProductVendor;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductVendorsImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?ProductVendor
    {
        $name = trim((string) ($row['name'] ?? $row['nama_vendor'] ?? ''));

        if ($name === '') {
            return null;
        }

        return ProductVendor::firstOrCreate(
            ['name' => $name],
            [
                'phone' => $row['phone'] ?? $row['telepon'] ?? null,
                'email' => $row['email'] ?? null,
                'address' => $row['address'] ?? $row['alamat'] ?? null,
                'description' => $row['description'] ?? $row['deskripsi'] ?? null,
            ]
        );
    }

    public function rules(): array
    {
        return [
            '*.name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
