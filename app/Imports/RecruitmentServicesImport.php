<?php

namespace App\Imports;

use App\Models\LP\RecruitmentService;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class RecruitmentServicesImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?RecruitmentService
    {
        $title = trim((string) ($row['title'] ?? $row['judul'] ?? ''));

        if ($title === '') {
            return null;
        }

        $slug = trim((string) ($row['slug'] ?? '')) ?: Str::slug($title);

        $rawActive = $row['is_active'] ?? $row['status'] ?? 1;
        $isActive = is_string($rawActive)
            ? in_array(strtolower(trim($rawActive)), ['1', 'true', 'yes', 'active', 'aktif'], true)
            : (bool) $rawActive;

        return RecruitmentService::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'category_key' => trim((string) ($row['category_key'] ?? 'Other')) ?: 'Other',
                'title' => $title,
                'subtitle' => $row['subtitle'] ?? null,
                'icon' => trim((string) ($row['icon'] ?? 'briefcase')) ?: 'briefcase',
                'image' => $row['image'] ?? null,
                'short_description' => $row['short_description'] ?? null,
                'description' => $row['description'] ?? null,
                'responsibilities' => $this->splitList($row['responsibilities'] ?? null),
                'skills' => $this->splitList($row['skills'] ?? null),
                'benefits' => $this->splitList($row['benefits'] ?? null),
                'order' => (int) ($row['order'] ?? 0),
                'is_active' => $isActive,
            ]
        );
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            '*.title' => ['nullable', 'string', 'max:150'],
            '*.category_key' => ['nullable', 'string', 'max:60'],
            '*.icon' => ['nullable', 'string', 'max:100'],
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
