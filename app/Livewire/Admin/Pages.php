<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use App\Services\ActivityLogger;
use App\Services\ImageOptimizer;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class Pages extends Component
{
    use WithFileUploads;

    // Form inputs
    public array $titles = [];
    public string $slug = '';
    public array $contents = [];
    public string $status = 'draft';
    public array $meta_titles = [];
    public array $meta_descriptions = [];

    public $featured_image;
    public ?string $existing_featured_image = null;

    // Per-page theme overrides (8 keys, nullable = inherit global)
    public ?string $theme_body_bg = null;
    public ?string $theme_body_text = null;
    public ?string $theme_header_bg = null;
    public ?string $theme_footer_bg = null;
    public ?string $theme_primary = null;
    public ?string $theme_accent = null;
    public ?string $theme_section_bg = null;
    public ?string $theme_card_bg = null;

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
            'featured_image' => 'nullable|image|max:2048',
            'theme_body_bg' => 'nullable|string|max:9|starts_with:#',
            'theme_body_text' => 'nullable|string|max:9|starts_with:#',
            'theme_header_bg' => 'nullable|string|max:9|starts_with:#',
            'theme_footer_bg' => 'nullable|string|max:9|starts_with:#',
            'theme_primary' => 'nullable|string|max:9|starts_with:#',
            'theme_accent' => 'nullable|string|max:9|starts_with:#',
            'theme_section_bg' => 'nullable|string|max:9|starts_with:#',
            'theme_card_bg' => 'nullable|string|max:9|starts_with:#',
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
        $this->featured_image = null;
        $this->existing_featured_image = null;
        $this->editingPageId = null;

        $this->theme_body_bg = null;
        $this->theme_body_text = null;
        $this->theme_header_bg = null;
        $this->theme_footer_bg = null;
        $this->theme_primary = null;
        $this->theme_accent = null;
        $this->theme_section_bg = null;
        $this->theme_card_bg = null;
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
        $this->existing_featured_image = $page->featured_image;

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

        $this->theme_body_bg = $page->theme_body_bg;
        $this->theme_body_text = $page->theme_body_text;
        $this->theme_header_bg = $page->theme_header_bg;
        $this->theme_footer_bg = $page->theme_footer_bg;
        $this->theme_primary = $page->theme_primary;
        $this->theme_accent = $page->theme_accent;
        $this->theme_section_bg = $page->theme_section_bg;
        $this->theme_card_bg = $page->theme_card_bg;
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

        if ($this->featured_image) {
            $optimizer = new ImageOptimizer();
            $pageData['featured_image'] = $optimizer->convertToWebp($this->featured_image, 'pages');
        }

        $pageData['theme_body_bg'] = $this->theme_body_bg ?: null;
        $pageData['theme_body_text'] = $this->theme_body_text ?: null;
        $pageData['theme_header_bg'] = $this->theme_header_bg ?: null;
        $pageData['theme_footer_bg'] = $this->theme_footer_bg ?: null;
        $pageData['theme_primary'] = $this->theme_primary ?: null;
        $pageData['theme_accent'] = $this->theme_accent ?: null;
        $pageData['theme_section_bg'] = $this->theme_section_bg ?: null;
        $pageData['theme_card_bg'] = $this->theme_card_bg ?: null;

        if ($this->editingPageId) {
            $page = Page::findOrFail($this->editingPageId);
            $page->update($pageData);

            ActivityLogger::log('page_updated', "Updated page: {$page->slug}");
            app(\App\Services\PageService::class)->clearCache($page->slug);
            session()->flash('message', 'Page updated successfully.');
        } else {
            $page = Page::create($pageData);

            ActivityLogger::log('page_created', "Created page: {$page->slug}");
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

        ActivityLogger::log('page_deleted', "Deleted page: {$slug}");
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
