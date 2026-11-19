<?php

namespace App\Imports;

use App\Models\LP\WhyChooseUs;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class WhyChooseUsImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?WhyChooseUs
    {
        $title = trim((string) ($row['title'] ?? $row['judul'] ?? ''));

        if ($title === '') {
            return null;
        }

        $rawActive = $row['is_active'] ?? $row['status'] ?? 1;
        $isActive = is_string($rawActive)
            ? in_array(strtolower(trim($rawActive)), ['1', 'true', 'yes', 'active', 'aktif'], true)
            : (bool) $rawActive;

        return WhyChooseUs::query()->updateOrCreate(
            ['title' => $title],
            [
                'description' => $row['description'] ?? $row['deskripsi'] ?? null,
                'icon' => trim((string) ($row['icon'] ?? 'shield-check')) ?: 'shield-check',
                'order' => (int) ($row['order'] ?? $row['urutan'] ?? 0),
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
            '*.icon' => ['nullable', 'string', 'max:100'],
        ];
    }
}
