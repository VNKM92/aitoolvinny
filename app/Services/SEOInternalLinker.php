<?php

namespace App\Services;

use App\Models\SEOKeyword;
use Illuminate\Support\Facades\Cache;

class SEOInternalLinker
{
    /**
     * Parse HTML content and inject internal links for predefined keywords.
     */
    public static function link(string $html): string
    {
        if (empty($html)) {
            return $html;
        }

        // Fetch keywords from cache/database
        $keywords = Cache::remember('seo_internal_keywords', 3600, function () {
            return SEOKeyword::all()->toArray();
        });

        if (empty($keywords)) {
            return $html;
        }

        // Use DOMDocument to parse HTML safely without breaking tags
        $dom = new \DOMDocument();
        // Suppress HTML parsing warnings
        libxml_use_internal_errors(true);
        // Load HTML with UTF-8 encoding
        $dom->loadHTML('<div>' . mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8') . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        // Fetch all text nodes that are NOT inside existing link, header, or attribute elements
        $textNodes = $xpath->query('//text()[not(ancestor::a) and not(ancestor::h1) and not(ancestor::h2) and not(ancestor::h3) and not(ancestor::h4) and not(ancestor::h5) and not(ancestor::h6) and not(ancestor::script) and not(ancestor::style)]');

        foreach ($textNodes as $node) {
            $text = $node->nodeValue;
            $replaced = false;

            foreach ($keywords as $kw) {
                $word = preg_quote($kw['keyword'], '/');
                // Regex for matching keyword as a whole word (case-insensitive)
                $pattern = '/\b(' . $word . ')\b/i';

                if (preg_match($pattern, $text)) {
                    // Create a link node
                    $replacementHtml = preg_replace($pattern, '<a href="' . htmlspecialchars($kw['url']) . '" class="text-primary hover:underline font-semibold">$1</a>', $text, 1);
                    
                    // Replace the text node with parsed HTML fragment
                    $fragment = $dom->createDocumentFragment();
                    $fragment->appendXML($replacementHtml);
                    $node->parentNode->replaceChild($fragment, $node);
                    
                    $replaced = true;
                    break; // Link only one keyword per text node to avoid over-optimization
                }
            }
        }

        // Output inside of wrapping div container
        $container = $dom->getElementsByTagName('div')->item(0);
        $outputHtml = '';
        foreach ($container->childNodes as $child) {
            $outputHtml .= $dom->saveHTML($child);
        }

        return $outputHtml;
    }
}
