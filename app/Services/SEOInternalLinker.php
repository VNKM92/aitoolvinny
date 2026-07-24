<?php

namespace App\Services;

use App\Models\SEOKeyword;
use App\Models\AffiliateLink;
use Illuminate\Support\Facades\Cache;

class SEOInternalLinker
{
    /**
     * Parse HTML content and inject internal/affiliate links for predefined keywords.
     */
    public static function link(string $html): string
    {
        if (empty($html)) {
            return $html;
        }

        // Fetch SEO keywords from cache/database
        $keywords = Cache::remember('seo_internal_keywords', 3600, function () {
            return SEOKeyword::all()->toArray();
        });

        // Fetch Cloaked Affiliate links from cache/database
        $affiliates = Cache::remember('seo_affiliate_links', 3600, function () {
            return AffiliateLink::all()->toArray();
        });

        $mergedKeywords = [];

        // Add regular keywords
        foreach ($keywords as $kw) {
            $mergedKeywords[] = [
                'keyword' => $kw['keyword'],
                'url' => $kw['url'],
                'class' => 'text-primary hover:underline font-semibold'
            ];
        }

        // Add affiliate keywords pointing to cloaked local redirect /go/{slug}
        foreach ($affiliates as $aff) {
            $mergedKeywords[] = [
                'keyword' => $aff['keyword'],
                'url' => '/go/' . $aff['slug'],
                'class' => 'text-pink-500 hover:underline font-semibold'
            ];
        }

        if (empty($mergedKeywords)) {
            return $html;
        }

        // Use DOMDocument to parse HTML safely without breaking tags
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<div>' . mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8') . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        // Fetch all text nodes that are NOT inside existing links or headers
        $textNodes = $xpath->query('//text()[not(ancestor::a) and not(ancestor::h1) and not(ancestor::h2) and not(ancestor::h3) and not(ancestor::h4) and not(ancestor::h5) and not(ancestor::h6) and not(ancestor::script) and not(ancestor::style)]');

        foreach ($textNodes as $node) {
            $text = $node->nodeValue;
            $replaced = false;

            foreach ($mergedKeywords as $kw) {
                $word = preg_quote($kw['keyword'], '/');
                $pattern = '/\b(' . $word . ')\b/i';

                if (preg_match($pattern, $text)) {
                    // Create link HTML
                    $replacementHtml = preg_replace($pattern, '<a href="' . htmlspecialchars($kw['url']) . '" class="' . $kw['class'] . '">$1</a>', $text, 1);
                    
                    $fragment = $dom->createDocumentFragment();
                    $fragment->appendXML($replacementHtml);
                    $node->parentNode->replaceChild($fragment, $node);
                    
                    $replaced = true;
                    break; // Link only one keyword per text node to prevent over-optimization
                }
            }
        }

        $container = $dom->getElementsByTagName('div')->item(0);
        $outputHtml = '';
        foreach ($container->childNodes as $child) {
            $outputHtml .= $dom->saveHTML($child);
        }

        return $outputHtml;
    }
}
