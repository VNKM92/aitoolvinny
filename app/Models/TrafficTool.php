<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficTool extends Model
{
    use HasFactory;

    protected $table = 'traffic_tools';

    protected $fillable = [
        'slug',
        'name',
        'description',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Helper to get localized attribute.
     */
    public function translate(string $attribute, string $locale = 'en'): string
    {
        $value = $this->{$attribute};

        if (is_array($value)) {
            return $value[$locale] ?? $value['en'] ?? reset($value) ?: '';
        }

        return (string) $value;
    }
}
