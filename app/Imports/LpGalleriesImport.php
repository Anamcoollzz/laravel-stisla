<?php

namespace App\Imports;

use App\Models\LP\LpGallery;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LpGalleriesImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?LpGallery
    {
        $title = trim((string) ($row['title'] ?? $row['judul'] ?? ''));

        if ($title === '') {
            return null;
        }

        $rawActive = $row['is_active'] ?? $row['status'] ?? 1;
        $isActive = is_string($rawActive)
            ? in_array(strtolower(trim($rawActive)), ['1', 'true', 'yes', 'active', 'aktif'], true)
            : (bool) $rawActive;

        return LpGallery::query()->updateOrCreate(
            ['title' => $title],
            [
                'category' => trim((string) ($row['category'] ?? $row['kategori'] ?? 'Company Activities')) ?: 'Company Activities',
                'date' => $row['date'] ?? $row['tanggal'] ?? null,
                'description' => $row['description'] ?? $row['deskripsi'] ?? null,
                'image' => $row['image'] ?? null,
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
            '*.category' => ['nullable', 'string', 'max:100'],
        ];
    }
}
