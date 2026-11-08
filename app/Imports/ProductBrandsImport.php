<?php

namespace App\Imports;

use App\Models\ProductBrand;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductBrandsImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?ProductBrand
    {
        $name = trim((string) ($row['name'] ?? $row['nama_brand'] ?? ''));

        if ($name === '') {
            return null;
        }

        $slug = trim((string) ($row['slug'] ?? ''));
        if ($slug === '') {
            $slug = Str::slug($name);
        }

        return ProductBrand::firstOrCreate(
            ['name' => $name],
            ['slug' => $slug]
        );
    }

    public function rules(): array
    {
        return [
            '*.name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
