<?php

namespace App\Http\Controllers;

use App\Services\PageService;
use App\Services\PostService;
use App\Services\TenantManager;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    protected TenantManager $tenantManager;
    protected PostService $postService;
    protected PageService $pageService;

    public function __construct(TenantManager $tenantManager, PostService $postService, PageService $pageService)
    {
        $this->tenantManager = $tenantManager;
        $this->postService = $postService;
        $this->pageService = $pageService;
    }

    /**
     * Generate sitemap.xml for current tenant.
     */
    public function index(): Response
    {
        $tenant = $this->tenantManager->getTenant();

        if (!$tenant) {
            abort(404);
        }

        $locales = $tenant->supported_locales ?? [$tenant->default_locale];
        $posts = $this->postService->getAllPosts()->filter(fn($p) => $p->status === 'published');
        $pages = $this->pageService->getPublishedPages();

        $urls = [];

        // Add home page for all locales
        foreach ($locales as $locale) {
            $urls[] = [
                'loc' => route('tenant.home', ['locale' => $locale]),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ];
        }

        // Add pages
        foreach ($pages as $page) {
            foreach ($locales as $locale) {
                $urls[] = [
                    'loc' => route('tenant.page', ['slug' => $page->slug, 'locale' => $locale]),
                    'lastmod' => $page->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            }
        }

        // Add posts
        foreach ($posts as $post) {
            foreach ($locales as $locale) {
                $urls[] = [
                    'loc' => route('tenant.post', ['slug' => $post->slug, 'locale' => $locale]),
                    'lastmod' => $post->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ];
            }
        }

        // Generate XML string
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "    <url>\n";
            $xml .= "        <loc>" . htmlspecialchars($url['loc']) . "</loc>\n";
            $xml .= "        <lastmod>" . $url['lastmod'] . "</lastmod>\n";
            $xml .= "        <changefreq>" . $url['changefreq'] . "</changefreq>\n";
            $xml .= "        <priority>" . $url['priority'] . "</priority>\n";
            $xml .= "    </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
