<?php

namespace App\Imports;

use App\Models\News\NewsTag;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class NewsTagsImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?NewsTag
    {
        $name = trim((string) ($row['name'] ?? $row['nama'] ?? ''));

        if ($name === '') {
            return null;
        }

        $rawSlug = trim((string) ($row['slug'] ?? ''));
        $slug = $rawSlug !== '' ? Str::slug($rawSlug) : Str::slug($name);
        if ($slug === '') {
            $slug = 'tag-'.uniqid();
        }

        return NewsTag::query()->firstOrCreate(
            ['slug' => $slug],
            ['name' => $name]
        );
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            '*.name' => ['nullable', 'string', 'max:100'],
        ];
    }
}
