<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'faqs';

    protected $fillable = [
        'question',
        'answer',
        'order',
    ];

    protected $casts = [
        'question' => 'array',
        'answer' => 'array',
    ];
}
