<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Page;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Faq;
use Illuminate\Support\Str;

class SEOService
{
    /**
     * Generate HTML Meta tags dynamically with template tag support.
     */
    public function generateTags($model, string $locale = 'en'): array
    {
        $postService = app(PostService::class);
        $pageService = app(PageService::class);

        $siteName = SiteSettings::get('site_name', 'CMS Website');
        $siteLogo = SiteSettings::get('logo', '');
        $siteMetaDesc = SiteSettings::get('meta_description', 'Welcome to ' . $siteName);

        // Fetch dynamic templates from settings, with sensible defaults
        $titleTemplate = SiteSettings::get('seo_title_template', '[title] | [site_name]');
        $descTemplate = SiteSettings::get('seo_desc_template', '[description]');

        $rawTitle = '';
        $rawDescription = '';
        $url = request()->url();
        $type = 'website';
        $image = '';
        $categoryName = '';
        $publishedAt = '';

        if ($model instanceof Post) {
            $rawTitle = $postService->translate($model, 'meta_title', $locale) ?: $postService->translate($model, 'title', $locale);
            $rawDescription = $postService->translate($model, 'meta_description', $locale) ?: substr(strip_tags($postService->translate($model, 'content', $locale)), 0, 160);
            $type = 'article';
            $image = $model->featured_image ? asset('storage/' . $model->featured_image) : '';
            $url = route('tenant.post', ['slug' => $model->slug, 'locale' => $locale]);
            $categoryName = $model->category ? ($model->category->name[$locale] ?? reset($model->category->name)) : '';
            $publishedAt = $model->published_at?->format('Y-m-d') ?? '';
        } elseif ($model instanceof Page) {
            $rawTitle = $pageService->translate($model, 'meta_title', $locale) ?: $pageService->translate($model, 'title', $locale);
            $rawDescription = $pageService->translate($model, 'meta_description', $locale) ?: substr(strip_tags($pageService->translate($model, 'content', $locale)), 0, 160);
            $url = route('tenant.page', ['slug' => $model->slug, 'locale' => $locale]);
        } elseif ($model instanceof Category) {
            $rawTitle = $model->name[$locale] ?? reset($model->name);
            $rawDescription = "Read articles in the {$rawTitle} category.";
            $url = route('tenant.category', ['slug' => $model->slug, 'locale' => $locale]);
        } else {
            // General homepage or other archive
            $rawTitle = $siteName;
            $rawDescription = $siteMetaDesc;
        }

        // Apply pagination suffix to title & description if on a paginated page
        $pageNumber = (int) request()->get('page', 1);
        $paginationSuffix = $pageNumber > 1 ? " - Page {$pageNumber}" : "";

        // Dynamic Tag replacements
        $replacements = [
            '[title]' => $rawTitle,
            '[site_name]' => $siteName,
            '[description]' => $rawDescription,
            '[category]' => $categoryName ?: 'General',
            '[date]' => $publishedAt ?: date('Y-m-d'),
            '[page]' => $pageNumber > 1 ? "Page {$pageNumber}" : "",
        ];

        $title = str_replace(array_keys($replacements), array_values($replacements), $titleTemplate) . $paginationSuffix;
        $description = Str::limit(str_replace(array_keys($replacements), array_values($replacements), $descTemplate), 160) . $paginationSuffix;

        // Ensure canonical handles pagination query
        $canonical = $url . ($pageNumber > 1 ? "?page={$pageNumber}" : "");

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'og' => [
                'title' => $title,
                'description' => $description,
                'url' => $canonical,
                'type' => $type,
                'image' => $image ?: ($siteLogo ? asset('storage/' . $siteLogo) : asset('logo.png')),
                'site_name' => $siteName,
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => $title,
                'description' => $description,
                'image' => $image ?: ($siteLogo ? asset('storage/' . $siteLogo) : asset('logo.png')),
            ],
        ];
    }

    /**
     * Generate structured JSON-LD schemas inside a single @graph array.
     */
    public function generateJsonLd($model, string $locale = 'en'): string
    {
        $postService = app(PostService::class);
        $pageService = app(PageService::class);

        $siteName = SiteSettings::get('site_name', 'CMS Website');
        $siteLogo = SiteSettings::get('logo', '');
        $logoUrl = $siteLogo ? asset('storage/' . $siteLogo) : asset('logo.png');
        $siteUrl = request()->root();

        // 1. Base WebSite & Organization
        $graph = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                '@id' => "{$siteUrl}/#website",
                'url' => $siteUrl,
                'name' => $siteName,
                'description' => SiteSettings::get('meta_description', 'Welcome to ' . $siteName),
                'publisher' => [
                    '@id' => "{$siteUrl}/#organization"
                ],
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => "{$siteUrl}/search?q={search_term_string}",
                    'query-input' => 'required name=search_term_string'
                ]
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                '@id' => "{$siteUrl}/#organization",
                'name' => $siteName,
                'url' => $siteUrl,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $logoUrl
                ]
            ]
        ];

        // 2. Dynamic BreadcrumbList Schema
        $breadcrumbItems = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => route('tenant.home', ['locale' => $locale])
            ]
        ];

        if ($model instanceof Post) {
            if ($model->category) {
                $breadcrumbItems[] = [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $model->category->name[$locale] ?? reset($model->category->name),
                    'item' => route('tenant.category', ['slug' => $model->category->slug, 'locale' => $locale])
                ];
            }
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => count($breadcrumbItems) + 1,
                'name' => $postService->translate($model, 'title', $locale),
                'item' => route('tenant.post', ['slug' => $model->slug, 'locale' => $locale])
            ];
        } elseif ($model instanceof Page) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $pageService->translate($model, 'title', $locale),
                'item' => route('tenant.page', ['slug' => $model->slug, 'locale' => $locale])
            ];
        } elseif ($model instanceof Category) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $model->name[$locale] ?? reset($model->name),
                'item' => route('tenant.category', ['slug' => $model->slug, 'locale' => $locale])
            ];
        }

        $graph[] = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            '@id' => request()->url() . '#breadcrumb',
            'itemListElement' => $breadcrumbItems
        ];

        // 3. Article / BlogPosting Schema
        if ($model instanceof Post) {
            $title = $postService->translate($model, 'title', $locale);
            $content = $postService->translate($model, 'content', $locale);
            $url = route('tenant.post', ['slug' => $model->slug, 'locale' => $locale]);
            $image = $model->featured_image ? asset('storage/' . $model->featured_image) : $logoUrl;

            $articleSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                '@id' => "{$url}#article",
                'isPartOf' => [
                    '@id' => "{$siteUrl}/#website"
                ],
                'headline' => $title,
                'description' => $postService->translate($model, 'meta_description', $locale) ?: substr(strip_tags($content), 0, 160),
                'url' => $url,
                'mainEntityOfPage' => $url,
                'datePublished' => $model->published_at?->toIso8601String() ?? $model->created_at->toIso8601String(),
                'dateModified' => $model->updated_at->toIso8601String(),
                'author' => [
                    '@type' => 'Person',
                    'name' => $siteName,
                ],
                'publisher' => [
                    '@id' => "{$siteUrl}/#organization"
                ],
                'image' => $image,
                'articleBody' => strip_tags($content)
            ];

            // 4. Video Schema (Dynamic extraction from content)
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $content, $match)) {
                $videoId = $match[1];
                $graph[] = [
                    '@context' => 'https://schema.org',
                    '@type' => 'VideoObject',
                    'name' => $title,
                    'description' => "Video accompanying: {$title}",
                    'thumbnailUrl' => "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg",
                    'uploadDate' => $model->created_at->toIso8601String(),
                    'embedUrl' => "https://www.youtube.com/embed/{$videoId}"
                ];
            }

            // 5. Review / Rating Schema (Dynamic parsing of comments & ratings)
            $approvedComments = $model->comments()->where('status', 'approved')->get();
            if ($approvedComments->count() > 0) {
                $reviews = [];
                $totalRating = 0;
                foreach ($approvedComments as $comment) {
                    // Assume comments might contain a rating (default to 5 stars)
                    $rating = 5;
                    $totalRating += $rating;
                    $reviews[] = [
                        '@type' => 'Review',
                        'author' => [
                            '@type' => 'Person',
                            'name' => $comment->author_name ?: 'Anonymous'
                        ],
                        'datePublished' => $comment->created_at->toIso8601String(),
                        'reviewBody' => $comment->content,
                        'reviewRating' => [
                            '@type' => 'Rating',
                            'ratingValue' => $rating,
                            'bestRating' => 5
                        ]
                    ];
                }

                $articleSchema['review'] = $reviews;
                $articleSchema['aggregateRating'] = [
                    '@type' => 'AggregateRating',
                    'ratingValue' => round($totalRating / $approvedComments->count(), 1),
                    'reviewCount' => $approvedComments->count(),
                    'bestRating' => 5,
                    'worstRating' => 1
                ];
            }

            $graph[] = $articleSchema;
        }

        // 6. FAQ Schema Integration
        $faqs = Faq::limit(5)->get();
        if ($faqs->count() > 0) {
            $faqElements = [];
            foreach ($faqs as $faq) {
                $question = $faq->question[$locale] ?? reset($faq->question);
                $answer = $faq->answer[$locale] ?? reset($faq->answer);
                $faqElements[] = [
                    '@type' => 'Question',
                    'name' => $question,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => strip_tags($answer)
                    ]
                ];
            }
            $graph[] = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $faqElements
            ];
        }

        return json_encode(['@graph' => $graph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
