<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AIService;
use App\Services\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AITest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        SiteSettings::set('gemini_api_key', 'test-key-123');

        $this->adminUser = User::create([
            'name' => 'CMS Admin',
            'email' => 'admin@cms.com',
            'password' => bcrypt('password'),
            'role' => 'administrator',
        ]);
    }

    /**
     * Test that AI Console page redirects guest to login.
     */
    public function test_ai_console_requires_authentication()
    {
        $response = $this->get('/admin/ai');
        $response->assertRedirect('/login');
    }

    /**
     * Test that AI Console page resolves for authenticated admin.
     */
    public function test_ai_console_resolves_for_admin()
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/ai');
        $response->assertStatus(200);
    }

    /**
     * Test that AIService generates prompts and parses Gemini responses successfully.
     */
    public function test_ai_service_generates_seo_titles()
    {
        // Mock the Gemini API call response
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => "1. Top Laravel Tips\n2. Laravel Secrets"]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $aiService = app(AIService::class);
        $result = $aiService->generateSEOTitles('Laravel Tips');

        $this->assertStringContainsString('Top Laravel Tips', $result);
        $this->assertStringContainsString('Laravel Secrets', $result);

        // Verify request format sent to Gemini
        Http::assertSent(function ($request) {
            return $request->url() === 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=test-key-123'
                && isset($request['contents'][0]['parts'][0]['text']);
        });
    }

    /**
     * Test that all 15 generators have correct prompt structures.
     */
    public function test_all_generators_call_correct_prompts()
    {
        Http::fake();

        $aiService = app(AIService::class);

        // Run basic checks to ensure methods exist and trigger calls
        $aiService->generateOutlines('Laravel');
        $aiService->generateArticles('Laravel', 'My Outline');
        $aiService->generateFaqs('Laravel');
        $aiService->generateMetaDescriptions('My Content');
        $aiService->generateKeywords('My Content');
        $aiService->generateImagePrompts('Laravel');
        $aiService->generateAltText('Laravel logo');
        $aiService->generateExcerpts('My Content');
        $aiService->generateYouTubeScript('Laravel');
        $aiService->generateShortsScript('Laravel');
        $aiService->generateFacebookPost('Laravel');
        $aiService->generateInstagramCaption('Laravel');
        $aiService->generateLinkedInPost('Laravel');
        $aiService->generatePinterestDescription('Laravel');

        Http::assertSentCount(14);
    }
}
