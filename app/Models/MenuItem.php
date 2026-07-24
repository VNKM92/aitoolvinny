<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'type', // link, post, page, category
        'url',
        'model_id',
        'order',
    ];

    protected $casts = [
        'title' => 'array',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    /**
     * Dynamically resolve item link URL.
     */
    public function getResolvedUrl(string $locale = 'en'): string
    {
        switch ($this->type) {
            case 'page':
                $page = Page::find($this->model_id);
                return $page ? route('tenant.page', ['slug' => $page->slug, 'locale' => $locale]) : '#';
            case 'post':
                $post = Post::find($this->model_id);
                return $post ? route('tenant.post', ['slug' => $post->slug, 'locale' => $locale]) : '#';
            case 'category':
                $category = Category::find($this->model_id);
                return $category ? route('tenant.category', ['slug' => $category->slug, 'locale' => $locale]) : '#';
            case 'link':
            default:
                return $this->url ?? '#';
        }
    }
}
