<?php

namespace App\Livewire\Admin;

use App\Models\TrafficTool;
use Livewire\Component;
use Livewire\WithPagination;

class ToolsManager extends Component
{
    use WithPagination;

    public bool $isEditing = false;
    public ?int $editingToolId = null;

    // Form inputs
    public string $toolSlug = '';
    public string $toolNameEn = '';
    public string $toolDescEn = '';
    public string $toolMetaTitleEn = '';
    public string $toolMetaDescEn = '';
    public bool $toolIsActive = true;

    public function editTool(int $id)
    {
        $tool = TrafficTool::findOrFail($id);
        $this->editingToolId = $id;
        $this->toolSlug = $tool->slug;
        $this->toolNameEn = $tool->name['en'] ?? '';
        $this->toolDescEn = $tool->description['en'] ?? '';
        $this->toolMetaTitleEn = $tool->meta_title['en'] ?? '';
        $this->toolMetaDescEn = $tool->meta_description['en'] ?? '';
        $this->toolIsActive = $tool->is_active;

        $this->isEditing = true;
    }

    public function saveTool()
    {
        $this->validate([
            'toolNameEn' => 'required|string|max:150',
            'toolDescEn' => 'required|string|max:500',
            'toolMetaTitleEn' => 'required|string|max:150',
            'toolMetaDescEn' => 'required|string|max:300',
        ]);

        $tool = TrafficTool::findOrFail($this->editingToolId);

        $tool->update([
            'name' => ['en' => $this->toolNameEn],
            'description' => ['en' => $this->toolDescEn],
            'meta_title' => ['en' => $this->toolMetaTitleEn],
            'meta_description' => ['en' => $this->toolMetaDescEn],
            'is_active' => $this->toolIsActive,
        ]);

        session()->flash('message', "Tool '{$tool->slug}' updated successfully.");
        
        $this->cancelEdit();
    }

    public function toggleToolStatus(int $id)
    {
        $tool = TrafficTool::findOrFail($id);
        $tool->update(['is_active' => !$tool->is_active]);
        session()->flash('message', "Status of '{$tool->slug}' toggled.");
    }

    public function cancelEdit()
    {
        $this->editingToolId = null;
        $this->isEditing = false;
        $this->reset(['toolSlug', 'toolNameEn', 'toolDescEn', 'toolMetaTitleEn', 'toolMetaDescEn', 'toolIsActive']);
    }

    public function render()
    {
        $tools = TrafficTool::orderBy('slug', 'asc')->paginate(10);

        return view('livewire.admin.tools-manager', compact('tools'))
            ->layout('components.layouts.admin');
    }
}
