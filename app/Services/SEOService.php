<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Page;
use App\Models\Tenant;

class SEOService
{
    /**
     * Generate HTML Meta tags for a Page/Post.
     */
    public function generateTags($model, Tenant $tenant, string $locale = 'en'): array
    {
        $postService = app(PostService::class);
        $pageService = app(PageService::class);

        $title = '';
        $description = '';
        $url = request()->url();
        $type = 'website';
        $image = '';

        if ($model instanceof Post) {
            $title = $postService->translate($model, 'meta_title', $locale) ?: $postService->translate($model, 'title', $locale);
            $description = $postService->translate($model, 'meta_description', $locale) ?: substr(strip_tags($postService->translate($model, 'content', $locale)), 0, 160);
            $type = 'article';
            $image = $model->featured_image ? asset('storage/' . $model->featured_image) : '';
            $url = route('tenant.post', ['slug' => $model->slug, 'locale' => $locale]);
        } elseif ($model instanceof Page) {
            $title = $pageService->translate($model, 'meta_title', $locale) ?: $pageService->translate($model, 'title', $locale);
            $description = $pageService->translate($model, 'meta_description', $locale) ?: substr(strip_tags($pageService->translate($model, 'content', $locale)), 0, 160);
            $url = route('tenant.page', ['slug' => $model->slug, 'locale' => $locale]);
        } else {
            // General Tenant Site Metadata
            $title = $tenant->name;
            $description = $tenant->settings['meta_description'] ?? 'Welcome to ' . $tenant->name;
        }

        // Apply site brand name suffix
        $title = $title . ' | ' . $tenant->name;

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $url,
            'og' => [
                'title' => $title,
                'description' => $description,
                'url' => $url,
                'type' => $type,
                'image' => $image ?: ($tenant->settings['logo'] ?? ''),
                'site_name' => $tenant->name,
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => $title,
                'description' => $description,
                'image' => $image ?: ($tenant->settings['logo'] ?? ''),
            ],
        ];
    }

    /**
     * Generate JSON-LD Schema markup.
     */
    public function generateJsonLd($model, Tenant $tenant, string $locale = 'en'): string
    {
        $postService = app(PostService::class);
        $pageService = app(PageService::class);

        $schema = [];

        if ($model instanceof Post) {
            $title = $postService->translate($model, 'title', $locale);
            $content = $postService->translate($model, 'content', $locale);
            $url = route('tenant.post', ['slug' => $model->slug, 'locale' => $locale]);
            $image = $model->featured_image ? asset('storage/' . $model->featured_image) : null;

            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $title,
                'description' => $postService->translate($model, 'meta_description', $locale) ?: substr(strip_tags($content), 0, 160),
                'url' => $url,
                'datePublished' => $model->published_at?->toIso8601String() ?? $model->created_at->toIso8601String(),
                'dateModified' => $model->updated_at->toIso8601String(),
                'author' => [
                    '@type' => 'Organization',
                    'name' => $tenant->name,
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => $tenant->name,
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $tenant->settings['logo'] ?? asset('logo.png'),
                    ]
                ],
            ];

            if ($image) {
                $schema['image'] = $image;
            }
        } else {
            // General WebSite Schema
            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $tenant->name,
                'url' => request()->root(),
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => request()->root() . '/search?q={search_term_string}',
                    'query-input' => 'required name=search_term_string'
                ]
            ];
        }

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
