<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Services\TenantManager;
use Livewire\Component;
use Illuminate\Support\Str;

class Categories extends Component
{
    public array $names = []; // [locale => name]
    public string $slug = '';

    public ?int $editingCategoryId = null;
    public bool $isCreating = false;

    public array $supportedLocales = [];

    protected function rules(): array
    {
        return [
            'names.*' => 'required|string|max:100',
            'slug' => 'required|string|alpha_dash|unique:categories,slug,' . ($this->editingCategoryId ?: 'NULL'),
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
        $this->names = [];
        foreach ($this->supportedLocales as $locale) {
            $this->names[$locale] = '';
        }
        $this->slug = '';
        $this->editingCategoryId = null;
    }

    public function updatedNames($value, $key)
    {
        // Auto-generate slug from primary locale (usually first in array or default)
        $tenant = app(TenantManager::class)->getTenant();
        $primaryLocale = $tenant->default_locale;
        if ($key === $primaryLocale) {
            $this->slug = Str::slug($value);
        }
    }

    public function editCategory(int $id)
    {
        $category = Category::findOrFail($id);
        $this->editingCategoryId = $id;
        $this->isCreating = true;
        
        $this->slug = $category->slug;
        $this->names = [];
        foreach ($this->supportedLocales as $locale) {
            $this->names[$locale] = $category->name[$locale] ?? '';
        }
    }

    public function toggleCreate()
    {
        $this->isCreating = !$this->isCreating;
        $this->resetInputFields();
    }

    public function saveCategory()
    {
        $this->validate();

        $categoryData = [
            'name' => $this->names,
            'slug' => $this->slug,
        ];

        if ($this->editingCategoryId) {
            $category = Category::findOrFail($this->editingCategoryId);
            $category->update($categoryData);
            session()->flash('message', 'Category updated successfully.');
        } else {
            Category::create($categoryData);
            session()->flash('message', 'Category created successfully.');
        }

        $this->isCreating = false;
        $this->resetInputFields();
    }

    public function deleteCategory(int $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        session()->flash('message', 'Category deleted successfully.');
    }

    public function render()
    {
        $categories = Category::orderBy('id', 'desc')->get();
        return view('livewire.admin.categories', compact('categories'))
            ->layout('components.layouts.admin');
    }
}
