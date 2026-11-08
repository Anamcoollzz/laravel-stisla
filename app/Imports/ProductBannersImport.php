<?php

namespace App\Imports;

use App\Models\ProductBanner;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductBannersImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?ProductBanner
    {
        $title = trim((string) ($row['title'] ?? $row['judul'] ?? ''));

        if ($title === '') {
            return null;
        }

        return ProductBanner::create([
            'title' => $title,
            'subtitle' => trim((string) ($row['subtitle'] ?? $row['deskripsi'] ?? '')) ?: null,
            'badge_label' => trim((string) ($row['badge_label'] ?? $row['badge'] ?? '')) ?: null,
            'button_text' => trim((string) ($row['button_text'] ?? 'Shop Now')) ?: 'Shop Now',
            'button_url' => trim((string) ($row['button_url'] ?? '#')) ?: '#',
            'position' => trim((string) ($row['position'] ?? 'hero')) ?: 'hero',
            'order' => isset($row['order']) ? (int) $row['order'] : 1,
            'is_active' => isset($row['is_active']) ? in_array(strtolower((string) $row['is_active']), ['1', 'true', 'yes', 'ya']) : true,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.title' => ['nullable', 'string', 'max:255'],
        ];
    }
}
