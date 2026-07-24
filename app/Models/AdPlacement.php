<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdPlacement extends Model
{
    use HasFactory;

    protected $table = 'ad_placements';

    protected $fillable = [
        'name',
        'type',
        'location',
        'code',
        'destination_url',
        'is_active',
        'impressions_count',
        'clicks_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'impressions_count' => 'integer',
        'clicks_count' => 'integer',
    ];

    /**
     * Get calculated Click-Through Rate (CTR) percentage.
     */
    public function getCtrAttribute(): float
    {
        if ($this->impressions_count === 0) {
            return 0.0;
        }

        return round(($this->clicks_count / $this->impressions_count) * 100, 2);
    }
}
