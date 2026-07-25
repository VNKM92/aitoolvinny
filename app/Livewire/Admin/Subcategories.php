<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Subcategory;
use App\Services\ActivityLogger;
use App\Services\ImageOptimizer;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class Subcategories extends Component
{
    use WithFileUploads;

    public ?int $category_id = null;
    public array $names = [];
    public array $descriptions = [];
    public array $meta_titles = [];
    public array $meta_descriptions = [];
    public string $slug = '';
    public bool $is_active = true;
    public int $sort_order = 0;
    public $featured_image;
    public ?string $existing_featured_image = null;

    public ?int $editingSubcategoryId = null;
    public bool $isCreating = false;
    public string $searchTerm = '';
    public ?int $filterCategoryId = null;

    public array $supportedLocales = [];

    protected function rules(): array
    {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'names.*' => 'required|string|max:100',
            'descriptions.*' => 'nullable|string|max:500',
            'meta_titles.*' => 'nullable|string|max:255',
            'meta_descriptions.*' => 'nullable|string|max:500',
            'slug' => 'required|string|alpha_dash|unique:subcategories,slug,' . ($this->editingSubcategoryId ?: 'NULL'),
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0|max:10000',
            'featured_image' => 'nullable|image|max:2048',
        ];
    }

    public function mount()
    {
        $this->supportedLocales = \App\Services\SiteSettings::get('supported_locales', ['en']);
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->names = [];
        $this->descriptions = [];
        $this->meta_titles = [];
        $this->meta_descriptions = [];

        foreach ($this->supportedLocales as $locale) {
            $this->names[$locale] = '';
            $this->descriptions[$locale] = '';
            $this->meta_titles[$locale] = '';
            $this->meta_descriptions[$locale] = '';
        }

        $this->category_id = null;
        $this->slug = '';
        $this->is_active = true;
        $this->sort_order = 0;
        $this->featured_image = null;
        $this->existing_featured_image = null;
        $this->editingSubcategoryId = null;
    }

    public function updatedNames($value, $key)
    {
        $primaryLocale = \App\Services\SiteSettings::get('default_locale', 'en');
        if ($key === $primaryLocale) {
            $this->slug = Str::slug($value);
        }
    }

    public function toggleCreate()
    {
        $this->isCreating = !$this->isCreating;
        $this->resetInputFields();
    }

    public function editSubcategory(int $id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $this->editingSubcategoryId = $id;
        $this->isCreating = true;

        $this->category_id = $subcategory->category_id;
        $this->slug = $subcategory->slug;
        $this->is_active = $subcategory->is_active;
        $this->sort_order = $subcategory->sort_order;
        $this->existing_featured_image = $subcategory->featured_image;

        foreach ($this->supportedLocales as $locale) {
            $this->names[$locale] = $subcategory->name[$locale] ?? '';
            $this->descriptions[$locale] = $subcategory->description[$locale] ?? '';
            $this->meta_titles[$locale] = $subcategory->meta_title[$locale] ?? '';
            $this->meta_descriptions[$locale] = $subcategory->meta_description[$locale] ?? '';
        }
    }

    public function saveSubcategory()
    {
        $this->validate();

        $data = [
            'category_id' => $this->category_id,
            'name' => $this->names,
            'description' => $this->descriptions,
            'slug' => $this->slug,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'meta_title' => $this->meta_titles,
            'meta_description' => $this->meta_descriptions,
        ];

        if ($this->featured_image) {
            $optimizer = new ImageOptimizer();
            $data['featured_image'] = $optimizer->convertToWebp($this->featured_image, 'subcategories');
        }

        if ($this->editingSubcategoryId) {
            $sc = Subcategory::findOrFail($this->editingSubcategoryId);
            $sc->update($data);
            ActivityLogger::log('subcategory_updated', "Updated subcategory: {$sc->slug}");
            session()->flash('message', 'Subcategory updated successfully.');
        } else {
            Subcategory::create($data);
            ActivityLogger::log('subcategory_created', "Created subcategory: {$this->slug}");
            session()->flash('message', 'Subcategory created successfully.');
        }

        $this->isCreating = false;
        $this->resetInputFields();
    }

    public function toggleActive(int $id)
    {
        $sc = Subcategory::findOrFail($id);
        $sc->update(['is_active' => !$sc->is_active]);
        session()->flash('message', 'Subcategory status updated.');
    }

    public function deleteSubcategory(int $id)
    {
        $sc = Subcategory::findOrFail($id);
        $slug = $sc->slug;
        $sc->delete();
        ActivityLogger::log('subcategory_deleted', "Deleted subcategory: {$slug}");
        session()->flash('message', 'Subcategory deleted successfully.');
    }

    public function render()
    {
        $query = Subcategory::with('category');

        if ($this->searchTerm) {
            $term = '%' . $this->searchTerm . '%';
            $query->where(function ($q) use ($term) {
                $q->where('slug', 'LIKE', $term)
                  ->orWhereRaw("JSON_EXTRACT(name, '$." . ($this->supportedLocales[0] ?? 'en') . "') LIKE ?", [$term]);
            });
        }

        if ($this->filterCategoryId) {
            $query->where('category_id', $this->filterCategoryId);
        }

        $subcategories = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        $categories = Category::orderBy('id', 'asc')->get(['id', 'name', 'slug']);

        return view('livewire.admin.subcategories', compact('subcategories', 'categories'))
            ->layout('components.layouts.admin');
    }
}
