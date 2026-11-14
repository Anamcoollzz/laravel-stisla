<?php

namespace App\Imports;

use App\Models\EcommerceFaq;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EcommerceFaqsImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?EcommerceFaq
    {
        $question = trim((string) ($row['question'] ?? $row['pertanyaan'] ?? ''));
        $answer = trim((string) ($row['answer'] ?? $row['jawaban'] ?? ''));

        if ($question === '' || $answer === '') {
            return null;
        }

        $column = strtolower(trim((string) ($row['column'] ?? $row['kolom'] ?? 'left')));
        if (! in_array($column, ['left', 'right'], true)) {
            $column = 'left';
        }

        return EcommerceFaq::create([
            'question' => $question,
            'answer' => $answer,
            'column' => $column,
            'order' => isset($row['order']) ? (int) $row['order'] : 1,
            'is_active' => isset($row['is_active']) ? in_array(strtolower((string) $row['is_active']), ['1', 'true', 'yes', 'ya']) : true,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.question' => ['nullable', 'string', 'max:255'],
            '*.answer' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
