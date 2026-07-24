<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use App\Services\TenantManager;
use Livewire\Component;
use Illuminate\Support\Str;

class Pages extends Component
{
    // Form inputs
    public array $titles = [];
    public string $slug = '';
    public array $contents = [];
    public string $status = 'draft';
    public array $meta_titles = [];
    public array $meta_descriptions = [];

    public ?int $editingPageId = null;
    public bool $isCreating = false;

    public array $supportedLocales = [];

    protected function rules(): array
    {
        return [
            'titles.*' => 'required|string|max:255',
            'slug' => 'required|string|alpha_dash|unique:pages,slug,' . ($this->editingPageId ?: 'NULL'),
            'contents.*' => 'required|string',
            'status' => 'required|in:draft,published',
            'meta_titles.*' => 'nullable|string|max:255',
            'meta_descriptions.*' => 'nullable|string|max:500',
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
        $this->status = 'draft';
        $this->editingPageId = null;
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

    public function editPage(int $id)
    {
        $page = Page::findOrFail($id);
        $this->editingPageId = $id;
        $this->isCreating = true;

        $this->slug = $page->slug;
        $this->status = $page->status;

        $this->titles = [];
        $this->contents = [];
        $this->meta_titles = [];
        $this->meta_descriptions = [];

        foreach ($this->supportedLocales as $locale) {
            $this->titles[$locale] = $page->title[$locale] ?? '';
            $this->contents[$locale] = $page->content[$locale] ?? '';
            $this->meta_titles[$locale] = $page->meta_title[$locale] ?? '';
            $this->meta_descriptions[$locale] = $page->meta_description[$locale] ?? '';
        }
    }

    public function savePage()
    {
        $this->validate();

        $pageData = [
            'title' => $this->titles,
            'slug' => $this->slug,
            'content' => $this->contents,
            'status' => $this->status,
            'meta_title' => $this->meta_titles,
            'meta_description' => $this->meta_descriptions,
        ];

        if ($this->editingPageId) {
            $page = Page::findOrFail($this->editingPageId);
            $page->update($pageData);

            // Clear page cache
            app(\App\Services\PageService::class)->clearCache($page->slug);
            session()->flash('message', 'Page updated successfully.');
        } else {
            Page::create($pageData);

            // Clear page cache
            app(\App\Services\PageService::class)->clearCache();
            session()->flash('message', 'Page created successfully.');
        }

        $this->isCreating = false;
        $this->resetInputFields();
    }

    public function deletePage(int $id)
    {
        $page = Page::findOrFail($id);
        $slug = $page->slug;
        $page->delete();

        // Clear page cache
        app(\App\Services\PageService::class)->clearCache($slug);
        session()->flash('message', 'Page deleted successfully.');
    }

    public function render()
    {
        $pages = Page::orderBy('id', 'desc')->get();
        return view('livewire.admin.pages', compact('pages'))
            ->layout('components.layouts.admin');
    }
}
