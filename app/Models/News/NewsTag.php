<?php

namespace App\Models\News;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug'])]
class NewsTag extends Model
{
    use HasFactory;

    /**
     * News articles associated with this tag.
     *
     * @return BelongsToMany<News, $this>
     */
    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'news_news_tag', 'news_tag_id', 'news_id');
    }
}
