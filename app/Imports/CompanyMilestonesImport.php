<?php

namespace App\Imports;

use App\Models\LP\CompanyMilestone;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CompanyMilestonesImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?CompanyMilestone
    {
        $year = trim((string) ($row['year'] ?? $row['tahun'] ?? ''));
        $title = trim((string) ($row['title'] ?? $row['judul'] ?? ''));

        if ($year === '' || $title === '') {
            return null;
        }

        $rawActive = $row['is_active'] ?? $row['status'] ?? 1;
        $isActive = is_string($rawActive)
            ? in_array(strtolower(trim($rawActive)), ['1', 'true', 'yes', 'active', 'aktif'], true)
            : (bool) $rawActive;

        return CompanyMilestone::query()->updateOrCreate(
            ['year' => $year, 'title' => $title],
            [
                'description' => $row['description'] ?? $row['deskripsi'] ?? null,
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
            '*.year' => ['nullable', 'string', 'max:30'],
            '*.title' => ['nullable', 'string', 'max:150'],
        ];
    }
}
