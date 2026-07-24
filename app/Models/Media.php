<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'filepath',
        'file_type',
        'file_size',
        'alt_text',
    ];

    protected $casts = [
        'alt_text' => 'array',
    ];
}
