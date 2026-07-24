<?php

namespace App\Livewire\Admin;

use App\Models\Popup;
use App\Services\ActivityLogger;
use App\Services\TenantManager;
use Livewire\Component;

class Popups extends Component
{
    public array $titles = [];
    public array $contents = [];
    public bool $is_active = true;
    public ?string $starts_at = null;
    public ?string $ends_at = null;

    public ?int $editingPopupId = null;
    public bool $isCreating = false;

    public array $supportedLocales = [];

    protected function rules(): array
    {
        return [
            'titles.*' => 'required|string|max:255',
            'contents.*' => 'required|string',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
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
        foreach ($this->supportedLocales as $locale) {
            $this->titles[$locale] = '';
            $this->contents[$locale] = '';
        }
        $this->is_active = true;
        $this->starts_at = null;
        $this->ends_at = null;
        $this->editingPopupId = null;
    }

    public function toggleCreate()
    {
        $this->isCreating = !$this->isCreating;
        $this->resetInputFields();
    }

    public function editPopup(int $id)
    {
        $popup = Popup::findOrFail($id);
        $this->editingPopupId = $id;
        $this->isCreating = true;

        $this->is_active = $popup->is_active;
        $this->starts_at = $popup->starts_at ? $popup->starts_at->format('Y-m-d\TH:i') : null;
        $this->ends_at = $popup->ends_at ? $popup->ends_at->format('Y-m-d\TH:i') : null;

        $this->titles = [];
        $this->contents = [];
        foreach ($this->supportedLocales as $locale) {
            $this->titles[$locale] = $popup->title[$locale] ?? '';
            $this->contents[$locale] = $popup->content[$locale] ?? '';
        }
    }

    public function savePopup()
    {
        $this->validate();

        $popupData = [
            'title' => $this->titles,
            'content' => $this->contents,
            'is_active' => $this->is_active,
            'starts_at' => $this->starts_at ?: null,
            'ends_at' => $this->ends_at ?: null,
        ];

        if ($this->editingPopupId) {
            $popup = Popup::findOrFail($this->editingPopupId);
            $popup->update($popupData);
            ActivityLogger::log('popup_updated', "Updated Popup campaign: #{$popup->id}");
            session()->flash('message', 'Popup updated successfully.');
        } else {
            $popup = Popup::create($popupData);
            ActivityLogger::log('popup_created', "Created Popup campaign: #{$popup->id}");
            session()->flash('message', 'Popup campaign saved.');
        }

        $this->isCreating = false;
        $this->resetInputFields();
    }

    public function togglePopupStatus(int $id)
    {
        $popup = Popup::findOrFail($id);
        $popup->update(['is_active' => !$popup->is_active]);
        ActivityLogger::log('popup_status_toggled', "Toggled status of popup: #{$popup->id}");
    }

    public function deletePopup(int $id)
    {
        $popup = Popup::findOrFail($id);
        $popup->delete();
        ActivityLogger::log('popup_deleted', "Deleted Popup campaign: #{$popup->id}");
        session()->flash('message', 'Popup campaign removed.');
    }

    public function render()
    {
        $popups = Popup::orderBy('id', 'desc')->get();
        return view('livewire.admin.popups', compact('popups'))
            ->layout('components.layouts.admin');
    }
}
