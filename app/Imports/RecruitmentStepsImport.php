<?php

namespace App\Imports;

use App\Models\LP\RecruitmentStep;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class RecruitmentStepsImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?RecruitmentStep
    {
        $title = trim((string) ($row['title'] ?? $row['judul'] ?? ''));
        $step = trim((string) ($row['step'] ?? $row['tahap'] ?? ''));

        if ($title === '' || $step === '') {
            return null;
        }

        $rawActive = $row['is_active'] ?? $row['status'] ?? 1;
        $isActive = is_string($rawActive)
            ? in_array(strtolower(trim($rawActive)), ['1', 'true', 'yes', 'active', 'aktif'], true)
            : (bool) $rawActive;

        return RecruitmentStep::query()->updateOrCreate(
            ['step' => $step, 'title' => $title],
            [
                'description' => $row['description'] ?? $row['deskripsi'] ?? null,
                'icon' => trim((string) ($row['icon'] ?? 'message-square')) ?: 'message-square',
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
            '*.step' => ['nullable', 'string', 'max:20'],
            '*.title' => ['nullable', 'string', 'max:150'],
            '*.icon' => ['nullable', 'string', 'max:100'],
        ];
    }
}
