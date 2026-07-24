<?php

namespace App\Services;

use App\Models\Page;
use App\Repositories\Contracts\PageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class PageService
{
    protected PageRepositoryInterface $pageRepo;

    public function __construct(PageRepositoryInterface $pageRepo)
    {
        $this->pageRepo = $pageRepo;
    }

    /**
     * Get dynamic translation for a page field.
     */
    public function translate(Page $page, string $field, ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $translations = $page->$field;

        if (is_array($translations)) {
            return $translations[$locale] ?? $translations[config('app.fallback_locale', 'en')] ?? '';
        }

        return $translations ?? '';
    }

    /**
     * Get all pages (cached).
     */
    public function getAllPages(): Collection
    {
        return Cache::remember("site_pages_all", now()->addHours(12), function () {
            return $this->pageRepo->all();
        });
    }

    /**
     * Get published pages (cached for navigation/footer).
     */
    public function getPublishedPages(): Collection
    {
        return Cache::remember("site_pages_published", now()->addHours(12), function () {
            return $this->pageRepo->getPublished();
        });
    }

    /**
     * Get individual page by slug (cached).
     */
    public function getPageBySlug(string $slug): ?Page
    {
        return Cache::remember("site_page_{$slug}", now()->addDays(1), function () use ($slug) {
            return $this->pageRepo->findBySlug($slug);
        });
    }

    /**
     * Create a page and clear cache.
     */
    public function createPage(array $data): Page
    {
        $page = $this->pageRepo->create($data);
        $this->clearCache();
        return $page;
    }

    /**
     * Update a page and clear cache.
     */
    public function updatePage(int $id, array $data): Page
    {
        $page = $this->pageRepo->update($id, $data);
        $this->clearCache($page->slug);
        return $page;
    }

    /**
     * Delete a page and clear cache.
     */
    public function deletePage(int $id): bool
    {
        $page = $this->pageRepo->find($id);
        $slug = $page ? $page->slug : null;
        $result = $this->pageRepo->delete($id);
        $this->clearCache($slug);
        return $result;
    }

    /**
     * Clear all page-related caches.
     */
    public function clearCache(?string $slug = null): void
    {
        Cache::forget("site_pages_all");
        Cache::forget("site_pages_published");

        if ($slug) {
            Cache::forget("site_page_{$slug}");
        }
    }
}
