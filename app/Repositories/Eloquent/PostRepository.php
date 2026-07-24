<?php

namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository implements PostRepositoryInterface
{
    public function all(): Collection
    {
        return Post::with('category')->orderBy('id', 'desc')->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Post::with('category')->orderBy('id', 'desc')->paginate($perPage);
    }

    public function find(int $id): ?Post
    {
        return Post::with('category')->find($id);
    }

    public function findBySlug(string $slug): ?Post
    {
        return Post::with('category')->where('slug', $slug)->first();
    }

    public function getPublished(): Collection
    {
        return Post::published()->with('category')->orderBy('published_at', 'desc')->get();
    }

    public function getPublishedPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Post::published()->with('category')->orderBy('published_at', 'desc')->paginate($perPage);
    }

    public function getPublishedByCategoryPaginated(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return Post::published()
            ->where('category_id', $categoryId)
            ->with('category')
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function update(int $id, array $data): Post
    {
        $post = Post::findOrFail($id);
        $post->update($data);
        return $post;
    }

    public function delete(int $id): bool
    {
        $post = Post::findOrFail($id);
        return $post->delete();
    }
}
