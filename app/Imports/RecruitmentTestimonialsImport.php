<?php

namespace App\Imports;

use App\Models\LP\RecruitmentTestimonial;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class RecruitmentTestimonialsImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?RecruitmentTestimonial
    {
        $name = trim((string) ($row['name'] ?? $row['nama'] ?? ''));
        $quote = trim((string) ($row['quote'] ?? $row['testimoni'] ?? ''));

        if ($name === '' || $quote === '') {
            return null;
        }

        $rawActive = $row['is_active'] ?? $row['status'] ?? 1;
        $isActive = is_string($rawActive)
            ? in_array(strtolower(trim($rawActive)), ['1', 'true', 'yes', 'active', 'aktif'], true)
            : (bool) $rawActive;

        $rating = max(1, min(5, (int) ($row['rating'] ?? 5)));

        return RecruitmentTestimonial::query()->updateOrCreate(
            ['name' => $name, 'service' => $row['service'] ?? $row['layanan'] ?? null],
            [
                'location' => $row['location'] ?? $row['lokasi'] ?? null,
                'quote' => $quote,
                'avatar' => $row['avatar'] ?? null,
                'rating' => $rating,
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
            '*.name' => ['nullable', 'string', 'max:150'],
            '*.rating' => ['nullable', 'integer', 'min:1', 'max:5'],
        ];
    }
}
