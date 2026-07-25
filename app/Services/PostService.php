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
            return $translations[$locale] ?? $translations[config('app.fallback_locale', 'en')] ?? array_values($translations)[0] ?? '';
        }

        return $translations ?? '';
    }

    /**
     * Get all posts (cached).
     */
    public function getAllPosts(): Collection
    {
        return Cache::remember("site_posts_all", now()->addHours(12), function () {
            return $this->postRepo->all();
        });
    }

    /**
     * Get published posts (cached for visitors).
     */
    public function getPublishedPostsPaginated(int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return Cache::remember("site_posts_published_p{$perPage}_page_{$page}", now()->addHours(2), function () use ($perPage) {
            return $this->postRepo->getPublishedPaginated($perPage);
        });
    }

    /**
     * Get published posts by category (cached for visitors).
     */
    public function getPublishedPostsByCategoryPaginated(int $categoryId, int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return Cache::remember("site_posts_cat_{$categoryId}_p{$perPage}_page_{$page}", now()->addHours(2), function () use ($categoryId, $perPage) {
            return $this->postRepo->getPublishedByCategoryPaginated($categoryId, $perPage);
        });
    }

    /**
     * Get individual post by slug (cached).
     */
    public function getPostBySlug(string $slug): ?Post
    {
        return Cache::remember("site_post_{$slug}", now()->addDays(1), function () use ($slug) {
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
     * Get N latest published posts (used by news ticker, homepage hero).
     */
    public function getLatestPublished(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("site_posts_latest_{$limit}", now()->addHour(), function () use ($limit) {
            return Post::published()
                ->with(['category', 'subcategory'])
                ->orderBy('published_at', 'desc')
                ->orderBy('id', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get featured (latest with featured_image) posts for hero section.
     */
    public function getFeaturedPublished(int $limit = 4): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("site_posts_featured_{$limit}", now()->addHour(), function () use ($limit) {
            return Post::published()
                ->with(['category', 'subcategory'])
                ->whereNotNull('featured_image')
                ->orderBy('published_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get related posts for a given post by category + subcategory + tags intersection.
     */
    public function getRelatedPosts(Post $post, int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("site_posts_related_{$post->id}_{$limit}", now()->addHours(6), function () use ($post, $limit) {
            $query = Post::published()
                ->with(['category', 'subcategory'])
                ->where('id', '!=', $post->id);

            if (!empty($post->subcategory_id)) {
                $subcatIds = is_array($post->subcategory_id) ? $post->subcategory_id : [$post->subcategory_id];
                $query->where(function ($q) use ($post, $subcatIds) {
                    $q->whereIn('subcategory_id', $subcatIds)
                      ->orWhere('category_id', $post->category_id);
                });
            } elseif (!empty($post->category_id)) {
                $query->where('category_id', $post->category_id);
            }

            $related = $query->orderBy('published_at', 'desc')->limit($limit)->get();

            if ($related->count() < $limit) {
                $excludeIds = $related->pluck('id')->push($post->id)->unique()->values()->all();
                $filler = Post::published()
                    ->with(['category', 'subcategory'])
                    ->whereNotIn('id', $excludeIds)
                    ->orderBy('published_at', 'desc')
                    ->limit($limit - $related->count())
                    ->get();
                $related = $related->merge($filler);
            }

            return $related;
        });
    }

    /**
     * Get published posts grouped by category for multi-section news homepage.
     *
     * @param int $perCategory Number of posts to fetch per category
     * @param int $maxCategories Maximum number of categories to include
     */
    public function getPublishedGroupedByCategory(int $perCategory = 5, int $maxCategories = 6): array
    {
        return Cache::remember("site_posts_grouped_cat_{$perCategory}_{$maxCategories}", now()->addHour(), function () use ($perCategory, $maxCategories) {
            $categories = \App\Models\Category::query()
                ->with(['posts' => function ($q) use ($perCategory) {
                    $q->published()
                      ->with(['subcategory'])
                      ->orderBy('published_at', 'desc')
                      ->limit($perCategory);
                }])
                ->orderBy('id', 'asc')
                ->take($maxCategories)
                ->get();

            $result = [];
            foreach ($categories as $cat) {
                if ($cat->posts->count() > 0) {
                    $result[$cat->slug] = [
                        'category' => $cat,
                        'posts' => $cat->posts,
                        'label' => null,
                    ];
                }
            }
            return $result;
        });
    }

    /**
     * Get published posts paginated by subcategory ID.
     */
    public function getPublishedPostsBySubcategoryPaginated(int $subcategoryId, int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return Cache::remember("site_posts_subcat_{$subcategoryId}_p{$perPage}_page_{$page}", now()->addHours(2), function () use ($subcategoryId, $perPage) {
            return Post::published()
                ->with(['category', 'subcategory'])
                ->where('subcategory_id', $subcategoryId)
                ->orderBy('published_at', 'desc')
                ->paginate($perPage);
        });
    }

    /**
     * Clear all post-related caches.
     */
    public function clearCache(?string $slug = null): void
    {
        Cache::forget("site_posts_all");

        for ($i = 1; $i <= 5; $i++) {
            Cache::forget("site_posts_published_p10_page_{$i}");
        }

        for ($i = 1; $i <= 5; $i++) {
            Cache::forget("site_posts_latest_{$i}");
            Cache::forget("site_posts_featured_{$i}");
        }

        Cache::forget("site_posts_grouped_cat_5");
        Cache::forget("site_posts_grouped_cat_6");
        for ($p = 3; $p <= 8; $p++) {
            for ($m = 3; $m <= 10; $m++) {
                Cache::forget("site_posts_grouped_cat_{$p}_{$m}");
            }
        }

        if ($slug) {
            Cache::forget("site_post_{$slug}");
        }

        $post = $slug ? $this->postRepo->findBySlug($slug) : null;
        if ($post) {
            for ($i = 3; $i <= 8; $i++) {
                Cache::forget("site_posts_related_{$post->id}_{$i}");
            }
        }
    }
}
