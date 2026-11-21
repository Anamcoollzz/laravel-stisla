<?php

namespace App\Imports;

use App\Models\News\News;
use App\Models\News\NewsComment;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class NewsCommentsImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?NewsComment
    {
        $slug = trim((string) ($row['news_slug'] ?? ''));
        $name = trim((string) ($row['name'] ?? ''));
        $email = trim((string) ($row['email'] ?? ''));
        $comment = trim((string) ($row['comment'] ?? ''));

        if ($name === '' || $email === '' || $comment === '') {
            return null;
        }

        $news = $slug !== '' ? News::query()->where('slug', $slug)->first() : News::query()->first();
        if (! $news) {
            return null;
        }

        return NewsComment::query()->create([
            'news_id' => $news->id,
            'name' => $name,
            'email' => $email,
            'website' => $row['website'] ?? null,
            'comment' => $comment,
            'is_approved' => isset($row['is_approved']) ? in_array(strtolower((string) $row['is_approved']), ['1', 'true', 'yes', 'ya', 'verified']) : false,
        ]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            '*.name' => ['nullable', 'string', 'max:120'],
            '*.email' => ['nullable', 'email', 'max:150'],
        ];
    }
}
