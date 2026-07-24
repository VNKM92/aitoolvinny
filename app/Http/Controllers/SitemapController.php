<?php

namespace App\Http\Controllers;

use App\Services\PageService;
use App\Services\PostService;
use App\Services\SiteSettings;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    protected PostService $postService;
    protected PageService $pageService;

    public function __construct(PostService $postService, PageService $pageService)
    {
        $this->postService = $postService;
        $this->pageService = $pageService;
    }

    /**
     * Generate main sitemap.xml.
     */
    public function index(): Response
    {
        $locales = SiteSettings::get('supported_locales', ['en']);
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

    /**
     * Generate Google Image Sitemap.
     */
    public function images(): Response
    {
        $posts = $this->postService->getAllPosts()->filter(fn($p) => $p->status === 'published' && !empty($p->featured_image));
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($posts as $post) {
            $url = route('tenant.post', ['slug' => $post->slug, 'locale' => app()->getLocale()]);
            $imageUrl = asset('storage/' . $post->featured_image);
            $title = $post->title[app()->getLocale()] ?? reset($post->title);

            $xml .= "    <url>\n";
            $xml .= "        <loc>" . htmlspecialchars($url) . "</loc>\n";
            $xml .= "        <image:image>\n";
            $xml .= "            <image:loc>" . htmlspecialchars($imageUrl) . "</image:loc>\n";
            $xml .= "            <image:title>" . htmlspecialchars($title) . "</image:title>\n";
            $xml .= "        </image:image>\n";
            $xml .= "    </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Generate Google News Sitemap (last 48 hours).
     */
    public function news(): Response
    {
        $posts = $this->postService->getAllPosts()
            ->filter(fn($p) => $p->status === 'published' && $p->published_at >= now()->subHours(48));

        $siteName = SiteSettings::get('site_name', 'CMS Website');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

        foreach ($posts as $post) {
            $url = route('tenant.post', ['slug' => $post->slug, 'locale' => app()->getLocale()]);
            $title = $post->title[app()->getLocale()] ?? reset($post->title);
            $date = $post->published_at->toIso8601String();

            $xml .= "    <url>\n";
            $xml .= "        <loc>" . htmlspecialchars($url) . "</loc>\n";
            $xml .= "        <news:news>\n";
            $xml .= "            <news:publication>\n";
            $xml .= "                <news:name>" . htmlspecialchars($siteName) . "</news:name>\n";
            $xml .= "                <news:language>" . app()->getLocale() . "</news:language>\n";
            $xml .= "            </news:publication>\n";
            $xml .= "            <news:publication_date>" . $date . "</news:publication_date>\n";
            $xml .= "            <news:title>" . htmlspecialchars($title) . "</news:title>\n";
            $xml .= "        </news:news>\n";
            $xml .= "    </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=1800',
        ]);
    }

    /**
     * Generate dynamic robots.txt.
     */
    public function robots(): Response
    {
        $siteUrl = request()->root();
        
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "Disallow: /admin\n";
        $robots .= "Disallow: /login\n";
        $robots .= "\n";
        $robots .= "Sitemap: {$siteUrl}/sitemap.xml\n";
        $robots .= "Sitemap: {$siteUrl}/sitemap-images.xml\n";
        $robots .= "Sitemap: {$siteUrl}/sitemap-news.xml\n";

        return response($robots, 200, [
            'Content-Type' => 'text/plain',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Generate dynamic RSS Feed.
     */
    public function feed(): Response
    {
        $posts = $this->postService->getAllPosts()->filter(fn($p) => $p->status === 'published')->take(20);
        $siteName = SiteSettings::get('site_name', 'CMS Website');
        $siteDesc = SiteSettings::get('meta_description', 'Welcome to ' . $siteName);
        $siteUrl = request()->root();

        $rss = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $rss .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        $rss .= '  <channel>' . "\n";
        $rss .= '    <title>' . htmlspecialchars($siteName) . '</title>' . "\n";
        $rss .= '    <link>' . $siteUrl . '</link>' . "\n";
        $rss .= '    <description>' . htmlspecialchars($siteDesc) . '</description>' . "\n";
        $rss .= '    <language>' . app()->getLocale() . '</language>' . "\n";
        $rss .= '    <atom:link href="' . $siteUrl . '/feed" rel="self" type="application/rss+xml" />' . "\n";

        foreach ($posts as $post) {
            $url = route('tenant.post', ['slug' => $post->slug, 'locale' => app()->getLocale()]);
            $title = $post->title[app()->getLocale()] ?? reset($post->title);
            $content = $post->content[app()->getLocale()] ?? reset($post->content);
            $date = $post->published_at ? $post->published_at->toRssString() : $post->created_at->toRssString();

            $rss .= '    <item>' . "\n";
            $rss .= '      <title>' . htmlspecialchars($title) . '</title>' . "\n";
            $rss .= '      <link>' . htmlspecialchars($url) . '</link>' . "\n";
            $rss .= '      <guid>' . htmlspecialchars($url) . '</guid>' . "\n";
            $rss .= '      <pubDate>' . $date . '</pubDate>' . "\n";
            $rss .= '      <description><![CDATA[' . $content . ']]></description>' . "\n";
            $rss .= '    </item>' . "\n";
        }

        $rss .= '  </channel>' . "\n";
        $rss .= '</rss>';

        return response($rss, 200, [
            'Content-Type' => 'application/rss+xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
