<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostRevision extends Model
{
    use HasFactory;

    // Disabling timestamps since we only track creation
    public $timestamps = false;

    protected $fillable = [
        'post_id',
        'user_id',
        'title',
        'content',
        'created_at',
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'created_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
