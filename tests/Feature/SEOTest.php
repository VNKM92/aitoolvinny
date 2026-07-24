<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Page;
use App\Models\SEORedirect;
use App\Models\SEO404Log;
use App\Models\SEOKeyword;
use App\Services\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SEOTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SiteSettings::set('site_name', 'Tech Enterprise SEO Blog');
        SiteSettings::set('default_locale', 'en');
        SiteSettings::set('supported_locales', ['en']);
    }

    /**
     * Test advanced sitemaps, robots.txt, and RSS feed.
     */
    public function test_seo_endpoints()
    {
        // 1. Robots.txt
        $responseRobots = $this->get('/robots.txt');
        $responseRobots->assertStatus(200);
        $responseRobots->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $responseRobots->assertSeeText('User-agent: *');
        $responseRobots->assertSeeText('Sitemap: http://localhost/sitemap.xml');

        // 2. RSS Feed
        $responseFeed = $this->get('/feed');
        $responseFeed->assertStatus(200);
        $responseFeed->assertHeader('Content-Type', 'application/rss+xml');

        // 3. Image Sitemap
        $responseImages = $this->get('/sitemap-images.xml');
        $responseImages->assertStatus(200);
        $responseImages->assertHeader('Content-Type', 'application/xml');

        // 4. News Sitemap
        $responseNews = $this->get('/sitemap-news.xml');
        $responseNews->assertStatus(200);
        $responseNews->assertHeader('Content-Type', 'application/xml');
    }

    /**
     * Test 301/302 redirects and 404 monitoring.
     */
    public function test_redirect_and_404_monitoring()
    {
        // Add redirect rule
        SEORedirect::create([
            'source_url' => '/old-slug',
            'target_url' => '/new-slug',
            'status_code' => 301
        ]);

        $response = $this->get('/old-slug');
        $response->assertRedirect('/new-slug');
        $response->assertStatus(301);

        // Hit a 404 URL and assert it's logged
        $response404 = $this->get('/some/broken/link');
        $response404->assertStatus(404);

        $this->assertDatabaseHas('seo_404_logs', [
            'url' => '/some/broken/link',
            'hits_count' => 1
        ]);
    }

    /**
     * Test internal linking and HTML image lazy loading.
     */
    public function test_internal_linking_and_html_optimizations()
    {
        // Set up keyword mapping
        SEOKeyword::create([
            'keyword' => 'Laravel',
            'url' => 'https://laravel.com'
        ]);

        $category = Category::create([
            'name' => ['en' => 'Programming'],
            'slug' => 'programming',
        ]);

        // Create post containing keyword and unoptimized image tag
        $post = Post::create([
            'category_id' => $category->id,
            'title' => ['en' => 'Learning Laravel'],
            'slug' => 'learning-laravel',
            'content' => ['en' => 'Here is a post about Laravel framework. <img src="test.jpg">'],
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/posts/learning-laravel');
        $response->assertStatus(200);

        // Assert keyword converted to link
        $response->assertSeeHtml('<a href="https://laravel.com" class="text-primary hover:underline font-semibold" target="_blank" rel="noopener noreferrer">Laravel</a>');

        // Assert image attributes injected
        $response->assertSeeHtml('loading="lazy"');
        $response->assertSeeHtml('decoding="async"');
    }
}
