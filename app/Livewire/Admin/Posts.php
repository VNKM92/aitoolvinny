<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Post;
use App\Services\TenantManager;
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

    public array $supportedLocales = [];

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
        ];
    }

    public function mount()
    {
        $tenant = app(TenantManager::class)->getTenant();
        $this->supportedLocales = $tenant->supported_locales ?? [$tenant->default_locale];
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
    }

    public function toggleCreate()
    {
        $this->isCreating = !$this->isCreating;
        $this->resetInputFields();
    }

    public function updatedTitles($value, $key)
    {
        $tenant = app(TenantManager::class)->getTenant();
        $primaryLocale = $tenant->default_locale;
        if ($key === $primaryLocale) {
            $this->slug = Str::slug($value);
        }
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

        // Handle image upload
        if ($this->featured_image) {
            $imagePath = $this->featured_image->store('posts', 'public');
            $postData['featured_image'] = $imagePath;
        }

        if ($this->editingPostId) {
            $post = Post::findOrFail($this->editingPostId);
            $post->update($postData);
            
            // Clear caches
            app(\App\Services\PostService::class)->clearCache($post->slug);
            session()->flash('message', 'Blog post updated successfully.');
        } else {
            $post = Post::create($postData);
            
            // Clear caches
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

        // Clear caches
        app(\App\Services\PostService::class)->clearCache($slug);
        session()->flash('message', 'Blog post deleted successfully.');
    }

    public function render()
    {
        // Category scope is active-tenant only under BelongsToTenant scope
        $categories = Category::all();
        $posts = Post::with('category')->orderBy('id', 'desc')->paginate(10);

        return view('livewire.admin.posts', compact('posts', 'categories'))
            ->layout('components.layouts.admin');
    }
}
