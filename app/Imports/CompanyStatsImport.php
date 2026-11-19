<?php

namespace App\Imports;

use App\Models\LP\CompanyStat;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CompanyStatsImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?CompanyStat
    {
        $label = trim((string) ($row['label'] ?? $row['judul'] ?? ''));
        $value = trim((string) ($row['value'] ?? $row['nilai'] ?? ''));

        if ($label === '' || $value === '') {
            return null;
        }

        $rawActive = $row['is_active'] ?? $row['status'] ?? 1;
        $isActive = is_string($rawActive)
            ? in_array(strtolower(trim($rawActive)), ['1', 'true', 'yes', 'active', 'aktif'], true)
            : (bool) $rawActive;

        return CompanyStat::query()->updateOrCreate(
            ['label' => $label],
            [
                'value' => $value,
                'description' => $row['description'] ?? $row['deskripsi'] ?? null,
                'icon' => trim((string) ($row['icon'] ?? 'award')) ?: 'award',
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
            '*.value' => ['nullable', 'string', 'max:50'],
            '*.label' => ['nullable', 'string', 'max:150'],
            '*.icon' => ['nullable', 'string', 'max:100'],
        ];
    }
}
