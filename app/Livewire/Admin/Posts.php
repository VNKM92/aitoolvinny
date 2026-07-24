<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\PostRevision;
use App\Services\TenantManager;
use App\Services\ActivityLogger;
use App\Services\ImageOptimizer;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Posts extends Component
{
    use WithFileUploads, WithPagination;

    // Form inputs
    public array $titles = [];
    public string $slug = '';
    public array $contents = [];
    public ?int $category_id = null;
    public string $status = 'draft';
    public array $meta_titles = [];
    public array $meta_descriptions = [];
    public bool $adsense_enabled = true;
    public ?string $published_at = null;
    
    public $featured_image;
    public ?string $existing_featured_image = null;

    public ?int $editingPostId = null;
    public bool $isCreating = false;

    // Tags list selection
    public array $selectedTags = [];

    // Revisions list
    public $revisions = [];

    // View Filter ('active' vs 'trash')
    public string $viewFilter = 'active';

    public array $supportedLocales = [];

    // Auto-save state message
    public string $lastSavedAt = '';

    protected function rules(): array
    {
        return [
            'titles.*' => 'required|string|max:255',
            'slug' => 'required|string|alpha_dash|unique:posts,slug,' . ($this->editingPostId ?: 'NULL'),
            'contents.*' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:draft,published',
            'meta_titles.*' => 'nullable|string|max:255',
            'meta_descriptions.*' => 'nullable|string|max:500',
            'adsense_enabled' => 'boolean',
            'published_at' => 'nullable|date',
            'featured_image' => 'nullable|image|max:2048', // 2MB max
            'selectedTags' => 'array',
        ];
    }

    public function mount()
    {
        $this->supportedLocales = \App\Services\SiteSettings::get('supported_locales', ['en']);
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->titles = [];
        $this->contents = [];
        $this->meta_titles = [];
        $this->meta_descriptions = [];

        foreach ($this->supportedLocales as $locale) {
            $this->titles[$locale] = '';
            $this->contents[$locale] = '';
            $this->meta_titles[$locale] = '';
            $this->meta_descriptions[$locale] = '';
        }

        $this->slug = '';
        $this->category_id = null;
        $this->status = 'draft';
        $this->adsense_enabled = true;
        $this->published_at = date('Y-m-d\TH:i'); // default to now
        $this->featured_image = null;
        $this->existing_featured_image = null;
        $this->editingPostId = null;
        $this->selectedTags = [];
        $this->revisions = [];
        $this->lastSavedAt = '';
    }

    public function toggleCreate()
    {
        $this->isCreating = !$this->isCreating;
        $this->resetInputFields();
    }

    public function updatedTitles($value, $key)
    {
        $primaryLocale = \App\Services\SiteSettings::get('default_locale', 'en');
        if ($key === $primaryLocale) {
            $this->slug = Str::slug($value);
        }
    }

    public function autoSave()
    {
        if (!$this->editingPostId || empty($this->titles)) {
            return;
        }

        // Auto-save creates a revision entry
        PostRevision::create([
            'post_id' => $this->editingPostId,
            'user_id' => auth()->id(),
            'title' => $this->titles,
            'content' => $this->contents,
            'created_at' => now(),
        ]);

        // Refresh revisions list
        $this->revisions = PostRevision::where('post_id', $this->editingPostId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $this->lastSavedAt = now()->format('H:i:s');
    }

    public function editPost(int $id)
    {
        $post = Post::findOrFail($id);
        $this->editingPostId = $id;
        $this->isCreating = true;

        $this->slug = $post->slug;
        $this->category_id = $post->category_id;
        $this->status = $post->status;
        $this->adsense_enabled = $post->adsense_enabled;
        $this->published_at = $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : null;
        $this->existing_featured_image = $post->featured_image;

        $this->titles = [];
        $this->contents = [];
        $this->meta_titles = [];
        $this->meta_descriptions = [];

        foreach ($this->supportedLocales as $locale) {
            $this->titles[$locale] = $post->title[$locale] ?? '';
            $this->contents[$locale] = $post->content[$locale] ?? '';
            $this->meta_titles[$locale] = $post->meta_title[$locale] ?? '';
            $this->meta_descriptions[$locale] = $post->meta_description[$locale] ?? '';
        }

        // Load Tags
        $this->selectedTags = $post->tags->pluck('id')->toArray();

        // Load Revisions
        $this->revisions = PostRevision::where('post_id', $id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    public function restoreRevision(int $revisionId)
    {
        $revision = PostRevision::findOrFail($revisionId);
        
        foreach ($this->supportedLocales as $locale) {
            $this->titles[$locale] = $revision->title[$locale] ?? '';
            $this->contents[$locale] = $revision->content[$locale] ?? '';
        }

        session()->flash('message', 'Revision content restored to editor. Click Save to commit changes.');
    }

    public function savePost()
    {
        $this->validate();

        $postData = [
            'category_id' => $this->category_id,
            'title' => $this->titles,
            'slug' => $this->slug,
            'content' => $this->contents,
            'status' => $this->status,
            'meta_title' => $this->meta_titles,
            'meta_description' => $this->meta_descriptions,
            'adsense_enabled' => $this->adsense_enabled,
            'published_at' => $this->published_at ?: null,
        ];

        // Handle image upload and optimization to WebP
        if ($this->featured_image) {
            $optimizer = new ImageOptimizer();
            $imagePath = $optimizer->convertToWebp($this->featured_image, 'posts');
            $postData['featured_image'] = $imagePath;
        }

        if ($this->editingPostId) {
            $post = Post::findOrFail($this->editingPostId);
            $post->update($postData);
            
            // Sync Tags
            $post->tags()->sync($this->selectedTags);

            // Record Manual Revision
            PostRevision::create([
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'title' => $this->titles,
                'content' => $this->contents,
                'created_at' => now(),
            ]);

            ActivityLogger::log('post_updated', "Updated blog post: {$post->slug}");
            app(\App\Services\PostService::class)->clearCache($post->slug);
            session()->flash('message', 'Blog post updated successfully.');
        } else {
            $post = Post::create($postData);
            
            // Sync Tags
            $post->tags()->sync($this->selectedTags);

            ActivityLogger::log('post_created', "Created blog post: {$post->slug}");
            app(\App\Services\PostService::class)->clearCache();
            session()->flash('message', 'Blog post published successfully.');
        }

        $this->isCreating = false;
        $this->resetInputFields();
    }

    public function deletePost(int $id)
    {
        $post = Post::findOrFail($id);
        $slug = $post->slug;
        $post->delete();

        ActivityLogger::log('post_trashed', "Moved post to trash: {$slug}");
        app(\App\Services\PostService::class)->clearCache($slug);
        session()->flash('message', 'Post moved to trash successfully.');
    }

    public function restorePost(int $id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->restore();

        ActivityLogger::log('post_restored', "Restored post from trash: {$post->slug}");
        app(\App\Services\PostService::class)->clearCache($post->slug);
        session()->flash('message', 'Post restored successfully.');
    }

    public function forceDeletePost(int $id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $slug = $post->slug;
        $post->forceDelete();

        ActivityLogger::log('post_force_deleted', "Permanently deleted post: {$slug}");
        session()->flash('message', 'Post permanently deleted.');
    }

    public function render()
    {
        $categories = Category::all();
        $tags = Tag::all();

        if ($this->viewFilter === 'trash') {
            $posts = Post::onlyTrashed()->with('category')->orderBy('id', 'desc')->paginate(10);
        } else {
            $posts = Post::with('category')->orderBy('id', 'desc')->paginate(10);
        }

        return view('livewire.admin.posts', compact('posts', 'categories', 'tags'))
            ->layout('components.layouts.admin');
    }
}
