<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'status',
        'meta_title',
        'meta_description',
        'adsense_enabled',
        'published_at',
        'theme_body_bg',
        'theme_body_text',
        'theme_header_bg',
        'theme_footer_bg',
        'theme_primary',
        'theme_accent',
        'theme_section_bg',
        'theme_card_bg',
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'excerpt' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'adsense_enabled' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(PostRevision::class)->orderBy('created_at', 'desc');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Scope for published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Scope for posts related to this post (by category/subcategory.
     */
    public function scopeRelatedTo($query, Post $post, int $limit = 6)
    {
        return $query->published()
            ->where('id', '!=', $post->id)
            ->where(function ($q) use ($post) {
                if ($post->subcategory_id) {
                    $q->where('subcategory_id', $post->subcategory_id);
                }
                if ($post->category_id) {
                    $q->orWhere('category_id', $post->category_id);
                }
            })
            ->orderBy('published_at', 'desc')
            ->limit($limit);
    }

    /**
     * Get safe excerpt (falls to auto-generated fallback if missing).
     * Standard Laravel accessor for $post->excerpt attribute.
     */
    public function getExcerptAttribute($value): string
    {
        $locale = app()->getLocale();
        return $this->excerptText($locale, 42);
    }

    /**
     * Get excerpt with explicit locale and word limit parameters.
     */
    public function excerptText(?string $locale = null, int $words = 42): string
    {
        $locale = $locale ?: app()->getLocale();
        $excerpts = $this->attributes['excerpt'] ?? null;

        if ($excerpts) {
            $ex = is_array($excerpts) ? $excerpts : json_decode($excerpts, true);
            if (is_array($ex) && !empty($ex[$locale])) {
                return $ex[$locale];
            }
            if (is_array($ex)) {
                $fallback = config('app.fallback_locale', 'en');
                if (!empty($ex[$fallback])) {
                    return $ex[$fallback];
                }
                $first = array_values($ex)[0] ?? null;
                if (is_string($first)) {
                    return $first;
                }
            }
        }

        $content = $this->content;
        if (is_array($content)) {
            $fallback = config('app.fallback_locale', 'en');
            $text = $content[$locale]
                ?? (isset($content[$fallback]) ? $content[$fallback] : (array_values($content)[0] ?? ''));
        } else {
            $text = (string) $content;
        }
        $text = strip_tags($text);
        $text = preg_replace('/\s+/u', ' ', $text);
        return \Illuminate\Support\Str::words(trim($text), $words, '...');
    }
}
