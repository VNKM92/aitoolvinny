<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateLink extends Model
{
    use HasFactory;

    protected $table = 'affiliate_links';

    protected $fillable = [
        'slug',
        'keyword',
        'target_url',
        'clicks_count',
    ];

    protected $casts = [
        'clicks_count' => 'integer',
    ];
}
