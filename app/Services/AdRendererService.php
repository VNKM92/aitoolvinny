<?php

namespace App\Services;

use App\Models\AdPlacement;
use Illuminate\Support\Facades\Cache;

class AdRendererService
{
    /**
     * Render an ad placement dynamically, supporting A/B testing and click/impression tracking.
     */
    public static function render(string $location): string
    {
        // 1. Fetch active ad placements for the location
        $ads = AdPlacement::where('location', $location)
            ->where('is_active', true)
            ->get();

        if ($ads->isEmpty()) {
            return '';
        }

        // 2. A/B Testing: Randomly select one active ad placement
        $selectedAd = $ads->random();

        // 3. Increment impressions count atomically in database
        try {
            $selectedAd->increment('impressions_count');
        } catch (\Throwable $e) {
            // Silence log errors
        }

        $code = $selectedAd->code;

        // 4. If it's a custom ad and has a destination URL, wrap it for click tracking
        if ($selectedAd->type === 'custom' && !empty($selectedAd->destination_url)) {
            // Rewrite standard links or wrap the code inside a tracker link
            $clickUrl = route('tenant.ad.click', ['id' => $selectedAd->id]);
            
            // Check if the code is already a simple image or simple HTML
            if (preg_match('/<img[^>]+>/i', $code) && !str_contains($code, '<a ')) {
                $code = '<a href="' . $clickUrl . '" target="_blank" class="block w-full h-full">' . $code . '</a>';
            }
        }

        return '<div class="ad-placement ad-' . $location . ' my-4 flex justify-center" data-ad-id="' . $selectedAd->id . '">' . $code . '</div>';
    }
}
