<?php

namespace App\Imports;

use App\Models\News\News;
use App\Models\News\NewsCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class NewsImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): ?News
    {
        $title = trim((string) ($row['title'] ?? $row['judul'] ?? ''));

        if ($title === '') {
            return null;
        }

        $rawSlug = trim((string) ($row['slug'] ?? ''));
        $baseSlug = $rawSlug !== '' ? Str::slug($rawSlug) : Str::slug($title);
        if ($baseSlug === '') {
            $baseSlug = 'news';
        }

        $slug = $baseSlug;
        $counter = 1;
        while (News::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }

        $categoryId = null;
        $categoryValue = trim((string) ($row['category'] ?? $row['kategori'] ?? $row['news_category_id'] ?? ''));
        if ($categoryValue !== '') {
            if (is_numeric($categoryValue)) {
                $categoryId = NewsCategory::query()->where('id', (int) $categoryValue)->value('id');
            } else {
                $category = NewsCategory::query()->firstOrCreate(['name' => $categoryValue]);
                $categoryId = $category->id;
            }
        }

        $status = strtolower(trim((string) ($row['status'] ?? 'draft')));
        if (! in_array($status, ['draft', 'published', 'archived'], true)) {
            $status = 'draft';
        }

        $publishedAt = trim((string) ($row['published_at'] ?? ''));
        if ($publishedAt === '' && $status === 'published') {
            $publishedAt = now()->toDateTimeString();
        }

        return News::query()->create([
            'title' => $title,
            'slug' => $slug,
            'content' => $row['content'] ?? $row['konten'] ?? null,
            'news_category_id' => $categoryId,
            'publisher_user_id' => Auth::id(),
            'status' => $status,
            'published_at' => $publishedAt !== '' ? $publishedAt : null,
        ]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            '*.title' => ['nullable', 'string', 'max:255'],
        ];
    }
}
