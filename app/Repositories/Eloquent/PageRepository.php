<?php

namespace App\Repositories\Eloquent;

use App\Models\Page;
use App\Repositories\Contracts\PageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PageRepository implements PageRepositoryInterface
{
    public function all(): Collection
    {
        return Page::orderBy('id', 'desc')->get();
    }

    public function find(int $id): ?Page
    {
        return Page::find($id);
    }

    public function findBySlug(string $slug): ?Page
    {
        return Page::where('slug', $slug)->first();
    }

    public function getPublished(): Collection
    {
        return Page::published()->orderBy('id', 'asc')->get();
    }

    public function create(array $data): Page
    {
        return Page::create($data);
    }

    public function update(int $id, array $data): Page
    {
        $page = Page::findOrFail($id);
        $page->update($data);
        return $page;
    }

    public function delete(int $id): bool
    {
        $page = Page::findOrFail($id);
        return $page->delete();
    }
}
