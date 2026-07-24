<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SEOKeyword extends Model
{
    use HasFactory;

    protected $table = 'seo_keywords';

    protected $fillable = [
        'keyword',
        'url',
    ];
}
