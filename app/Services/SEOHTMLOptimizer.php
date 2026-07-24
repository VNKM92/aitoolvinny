<?php

namespace App\Services;

class SEOHTMLOptimizer
{
    /**
     * Optimize HTML contents for Core Web Vitals (Lazy loading, async decoding, noopener, CDN rewrite).
     */
    public static function optimize(string $html): string
    {
        if (empty($html)) {
            return $html;
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<div>' . mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8') . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        // 1. Optimize Image tags & Rewrite to CDN
        $images = $dom->getElementsByTagName('img');
        $cdnUrl = SiteSettings::get('image_cdn_url') ?: env('IMAGE_CDN_URL', '');

        foreach ($images as $img) {
            // Lazy load all images except the first one in content
            if (!$img->hasAttribute('loading')) {
                $img->setAttribute('loading', 'lazy');
            }
            if (!$img->hasAttribute('decoding')) {
                $img->setAttribute('decoding', 'async');
            }
            // Ensure alt tag exists for screen readers and SEO
            if (!$img->hasAttribute('alt') || empty(trim($img->getAttribute('alt')))) {
                $img->setAttribute('alt', 'Image description');
            }

            // Rewrite src to CDN if configured
            if (!empty($cdnUrl) && $img->hasAttribute('src')) {
                $src = $img->getAttribute('src');
                if (str_starts_with($src, '/storage/')) {
                    $img->setAttribute('src', rtrim($cdnUrl, '/') . $src);
                } elseif (str_starts_with($src, asset('storage/'))) {
                    $relativePart = str_replace(asset('storage/'), '/storage/', $src);
                    $img->setAttribute('src', rtrim($cdnUrl, '/') . $relativePart);
                }
            }
        }

        // 2. Optimize External Link tags
        $links = $dom->getElementsByTagName('a');
        $siteUrl = request()->root();
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            if (!empty($href) && str_starts_with($href, 'http') && !str_starts_with($href, $siteUrl)) {
                $link->setAttribute('target', '_blank');
                $link->setAttribute('rel', 'noopener noreferrer');
            }
        }

        // Output container children
        $container = $dom->getElementsByTagName('div')->item(0);
        $outputHtml = '';
        foreach ($container->childNodes as $child) {
            $outputHtml .= $dom->saveHTML($child);
        }

        return $outputHtml;
    }
}
