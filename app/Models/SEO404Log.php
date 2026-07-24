<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SEO404Log extends Model
{
    use HasFactory;

    protected $table = 'seo_404_logs';

    protected $fillable = [
        'url',
        'referrer',
        'ip_address',
        'hits_count',
    ];
}
