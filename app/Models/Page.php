<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'featured_image',
        'status',
        'meta_title',
        'meta_description',
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
        'meta_title' => 'array',
        'meta_description' => 'array',
    ];

    /**
     * Scope for published pages.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
