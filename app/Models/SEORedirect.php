<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SEORedirect extends Model
{
    use HasFactory;

    protected $table = 'seo_redirects';

    protected $fillable = [
        'source_url',
        'target_url',
        'status_code',
    ];
}
