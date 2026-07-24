<?php

namespace App\Http\Controllers;

use App\Models\AdPlacement;
use App\Models\AffiliateLink;
use Illuminate\Http\RedirectResponse;

class MonetizationController extends Controller
{
    /**
     * Track ad click and redirect to destination URL.
     */
    public function adClick(int $id): RedirectResponse
    {
        $ad = AdPlacement::findOrFail($id);
        
        try {
            $ad->increment('clicks_count');
        } catch (\Throwable $e) {
            // Fail silently
        }

        $url = $ad->destination_url ?: '/';
        return redirect()->away($url);
    }

    /**
     * Track affiliate link click and redirect to destination affiliate URL.
     */
    public function affiliateRedirect(string $slug): RedirectResponse
    {
        $affiliate = AffiliateLink::where('slug', $slug)->firstOrFail();

        try {
            $affiliate->increment('clicks_count');
        } catch (\Throwable $e) {
            // Fail silently
        }

        return redirect()->away($affiliate->target_url);
    }
}
