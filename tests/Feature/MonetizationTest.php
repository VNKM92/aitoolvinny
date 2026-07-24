<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AdPlacement;
use App\Models\AffiliateLink;
use App\Services\AdRendererService;
use App\Services\SEOInternalLinker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MonetizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'CMS Admin',
            'email' => 'admin@cms.com',
            'password' => 'password',
            'role' => 'administrator',
        ]);
    }

    /**
     * Test admin monetization route requires auth and resolves.
     */
    public function test_admin_monetization_dashboard_access()
    {
        $this->get('/admin/monetization')->assertRedirect('/login');

        $response = $this->actingAs($this->adminUser)->get('/admin/monetization');
        $response->assertStatus(200);
    }

    /**
     * Test ad rendering, A/B testing, and impressions logging.
     */
    public function test_ad_rendering_and_ab_testing()
    {
        // 1. Create two active ads in the header slot for A/B Testing
        $adA = AdPlacement::create([
            'name' => 'Header Banner Ad A',
            'type' => 'custom',
            'location' => 'header',
            'code' => '<div class="ad-a">Ad Content A</div>',
            'is_active' => true,
        ]);

        $adB = AdPlacement::create([
            'name' => 'Header Banner Ad B',
            'type' => 'custom',
            'location' => 'header',
            'code' => '<div class="ad-b">Ad Content B</div>',
            'is_active' => true,
        ]);

        $this->assertEquals(0, $adA->impressions_count);
        $this->assertEquals(0, $adB->impressions_count);

        // 2. Trigger ad rendering
        $renderedCode = AdRendererService::render('header');

        $adA->refresh();
        $adB->refresh();

        // One of the ads must be selected (A/B testing) and its impression incremented
        $totalImpressions = $adA->impressions_count + $adB->impressions_count;
        $this->assertEquals(1, $totalImpressions);

        // Rendered content must contain one of the codes
        $this->assertTrue(
            str_contains($renderedCode, 'Ad Content A') || str_contains($renderedCode, 'Ad Content B')
        );
    }

    /**
     * Test ad click tracking.
     */
    public function test_ad_click_tracking()
    {
        $ad = AdPlacement::create([
            'name' => 'Sidebar Banner',
            'type' => 'custom',
            'location' => 'sidebar',
            'code' => 'Custom Ad Code',
            'destination_url' => 'https://external-advertiser.com/landing',
            'is_active' => true,
        ]);

        $this->assertEquals(0, $ad->clicks_count);

        $response = $this->get("/ad-click/{$ad->id}");
        $response->assertRedirect('https://external-advertiser.com/landing');

        $ad->refresh();
        $this->assertEquals(1, $ad->clicks_count);
    }

    /**
     * Test affiliate link redirects.
     */
    public function test_affiliate_redirect_tracking()
    {
        $aff = AffiliateLink::create([
            'slug' => 'hostinger-deal',
            'keyword' => 'Hostinger',
            'target_url' => 'https://hostinger.com/vk-discount',
        ]);

        $this->assertEquals(0, $aff->clicks_count);

        $response = $this->get('/go/hostinger-deal');
        $response->assertRedirect('https://hostinger.com/vk-discount');

        $aff->refresh();
        $this->assertEquals(1, $aff->clicks_count);
    }

    /**
     * Test dynamic auto-linking of affiliate keywords inside post bodies.
     */
    public function test_affiliate_keyword_auto_linking()
    {
        AffiliateLink::create([
            'slug' => 'book-deal',
            'keyword' => 'Amazon Book',
            'target_url' => 'https://amazon.com/book',
        ]);

        $html = '<p>Check out this amazing Amazon Book on coding.</p>';
        $linkedHtml = SEOInternalLinker::link($html);

        // Keyword should be auto-linked to cloaked local redirect /go/book-deal
        $this->assertStringContainsString('/go/book-deal', $linkedHtml);
        $this->assertStringContainsString('class="text-pink-500 hover:underline font-semibold"', $linkedHtml);
    }
}
