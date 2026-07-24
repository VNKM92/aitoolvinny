<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class PostService
{
    protected PostRepositoryInterface $postRepo;

    public function __construct(PostRepositoryInterface $postRepo)
    {
        $this->postRepo = $postRepo;
    }

    /**
     * Get dynamic translation for a post field.
     */
    public function translate(Post $post, string $field, ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $translations = $post->$field;

        if (is_array($translations)) {
            return $translations[$locale] ?? $translations[config('app.fallback_locale', 'en')] ?? '';
        }

        return $translations ?? '';
    }

    /**
     * Get all posts (cached).
     */
    public function getAllPosts(): Collection
    {
        $tenantId = app(TenantManager::class)->getTenantId() ?: 0;
        return Cache::remember("tenant_{$tenantId}_posts_all", now()->addHours(12), function () {
            return $this->postRepo->all();
        });
    }

    /**
     * Get published posts (cached for visitors).
     */
    public function getPublishedPostsPaginated(int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        $tenantId = app(TenantManager::class)->getTenantId() ?: 0;
        return Cache::remember("tenant_{$tenantId}_posts_published_p{$perPage}_page_{$page}", now()->addHours(2), function () use ($perPage) {
            return $this->postRepo->getPublishedPaginated($perPage);
        });
    }

    /**
     * Get published posts by category (cached for visitors).
     */
    public function getPublishedPostsByCategoryPaginated(int $categoryId, int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        $tenantId = app(TenantManager::class)->getTenantId() ?: 0;
        return Cache::remember("tenant_{$tenantId}_posts_cat_{$categoryId}_p{$perPage}_page_{$page}", now()->addHours(2), function () use ($categoryId, $perPage) {
            return $this->postRepo->getPublishedByCategoryPaginated($categoryId, $perPage);
        });
    }

    /**
     * Get individual post by slug (cached).
     */
    public function getPostBySlug(string $slug): ?Post
    {
        $tenantId = app(TenantManager::class)->getTenantId() ?: 0;
        return Cache::remember("tenant_{$tenantId}_post_{$slug}", now()->addDays(1), function () use ($slug) {
            return $this->postRepo->findBySlug($slug);
        });
    }

    /**
     * Create a post and clear cache.
     */
    public function createPost(array $data): Post
    {
        $post = $this->postRepo->create($data);
        $this->clearCache();
        return $post;
    }

    /**
     * Update a post and clear cache.
     */
    public function updatePost(int $id, array $data): Post
    {
        $post = $this->postRepo->update($id, $data);
        $this->clearCache($post->slug);
        return $post;
    }

    /**
     * Delete a post and clear cache.
     */
    public function deletePost(int $id): bool
    {
        $post = $this->postRepo->find($id);
        $slug = $post ? $post->slug : null;
        $result = $this->postRepo->delete($id);
        $this->clearCache($slug);
        return $result;
    }

    /**
     * Clear all post-related caches.
     */
    public function clearCache(?string $slug = null): void
    {
        $tenantId = app(TenantManager::class)->getTenantId() ?: 0;

        // Clear general caches
        Cache::forget("tenant_{$tenantId}_posts_all");

        // Clear paginated caches (we can flush cache tags or clear specific ones)
        // Since we are not using tag-supported drivers (like file/database by default in local),
        // we'll clear by searching or let them expire. Or we can clear common patterns:
        for ($i = 1; $i <= 5; $i++) {
            Cache::forget("tenant_{$tenantId}_posts_published_p10_page_{$i}");
        }

        if ($slug) {
            Cache::forget("tenant_{$tenantId}_post_{$slug}");
        }
    }
}
