<?php

namespace App\Imports;

use App\Models\News\NewsCategory;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class NewsCategoriesImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?NewsCategory
    {
        $name = trim((string) ($row['name'] ?? $row['nama'] ?? ''));

        if ($name === '') {
            return null;
        }

        return NewsCategory::query()->firstOrCreate(
            ['name' => $name],
            ['description' => $row['description'] ?? $row['deskripsi'] ?? null]
        );
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            '*.name' => ['nullable', 'string', 'max:150'],
        ];
    }
}
