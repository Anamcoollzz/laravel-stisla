<?php

namespace App\Imports;

use App\Models\LP\LpFaq;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LpFaqsImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?LpFaq
    {
        $question = trim((string) ($row['question'] ?? $row['pertanyaan'] ?? ''));
        $answer = trim((string) ($row['answer'] ?? $row['jawaban'] ?? ''));

        if ($question === '' || $answer === '') {
            return null;
        }

        $rawActive = $row['is_active'] ?? $row['status'] ?? 1;
        $isActive = is_string($rawActive)
            ? in_array(strtolower(trim($rawActive)), ['1', 'true', 'yes', 'active', 'aktif'], true)
            : (bool) $rawActive;

        return LpFaq::query()->updateOrCreate(
            ['question' => $question],
            [
                'answer' => $answer,
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
            '*.question' => ['nullable', 'string', 'max:255'],
        ];
    }
}
