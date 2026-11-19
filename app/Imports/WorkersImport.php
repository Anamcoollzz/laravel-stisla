<?php

namespace App\Imports;

use App\Models\LP\Worker;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class WorkersImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?Worker
    {
        $name = trim((string) ($row['name'] ?? $row['nama'] ?? ''));
        $code = trim((string) ($row['code'] ?? $row['kode'] ?? ''));

        if ($name === '' || $code === '') {
            return null;
        }

        $slug = trim((string) ($row['slug'] ?? '')) ?: Str::slug($name.'-'.$code);
        $rawFeatured = $row['is_featured'] ?? $row['featured'] ?? 0;
        $isFeatured = is_string($rawFeatured)
            ? in_array(strtolower(trim($rawFeatured)), ['1', 'true', 'yes', 'ya'], true)
            : (bool) $rawFeatured;

        $photo = trim((string) ($row['photo'] ?? '')) ?: null;

        return Worker::query()->updateOrCreate(
            ['code' => $code],
            [
                'slug' => $slug,
                'name' => $name,
                'category' => trim((string) ($row['category'] ?? 'ART')) ?: 'ART',
                'category_label' => trim((string) ($row['category_label'] ?? 'Domestic Helper (ART)')) ?: 'Domestic Helper (ART)',
                'age' => (int) ($row['age'] ?? 25),
                'age_bracket' => trim((string) ($row['age_bracket'] ?? '26-35')) ?: '26-35',
                'origin' => $row['origin'] ?? null,
                'origin_region' => $row['origin_region'] ?? null,
                'education' => $row['education'] ?? null,
                'experience_years' => (float) ($row['experience_years'] ?? 0),
                'experience_label' => $row['experience_label'] ?? null,
                'experience_bracket' => trim((string) ($row['experience_bracket'] ?? '1-3')) ?: '1-3',
                'status' => trim((string) ($row['status'] ?? 'Available')) ?: 'Available',
                'is_featured' => $isFeatured,
                'published_date' => $row['published_date'] ?? now()->toDateString(),
                'photo' => $photo,
                'gallery' => $photo ? [$photo] : [],
                'languages' => $this->splitList($row['languages'] ?? null),
                'bio' => $row['bio'] ?? null,
                'skills' => $this->splitList($row['skills'] ?? null),
            ]
        );
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            '*.code' => ['nullable', 'string', 'max:50'],
            '*.name' => ['nullable', 'string', 'max:150'],
            '*.age' => ['nullable', 'integer', 'min:17', 'max:75'],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function splitList(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value), fn (string $v): bool => $v !== ''));
        }

        $text = trim((string) $value);
        if ($text === '') {
            return [];
        }

        $parts = preg_split('/\s*\|\s*|\r\n|\r|\n/', $text) ?: [];

        return array_values(array_filter(array_map('trim', $parts), fn (string $v): bool => $v !== ''));
    }
}
