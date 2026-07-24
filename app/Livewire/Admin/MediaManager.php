<?php

namespace App\Livewire\Admin;

use App\Models\Media;
use App\Services\ImageOptimizer;
use App\Services\ActivityLogger;
use Livewire\Component;
use Livewire\WithFileUploads;

class MediaManager extends Component
{
    use WithFileUploads;

    public $file;
    public array $alt_text = [];
    public ?int $selectedMediaId = null;

    // View filter
    public string $search = '';

    protected array $rules = [
        'file' => 'required|file|max:5120', // 5MB max
        'alt_text.*' => 'nullable|string|max:255',
    ];

    public function uploadFile()
    {
        $this->validate(['file' => 'required|file|max:5120']);

        $optimizer = new ImageOptimizer();
        $isImage = str_starts_with($this->file->getMimeType(), 'image/');

        if ($isImage) {
            // Convert to optimized WebP
            $filepath = $optimizer->convertToWebp($this->file, 'media');
            $fileType = 'image/webp';
        } else {
            // Save standard attachment
            $filepath = $this->file->store('attachments', 'public');
            $fileType = $this->file->getClientMimeType();
        }

        // Initialize empty alt texts for tenant languages
        $altTexts = [];
        $supportedLocales = \App\Services\SiteSettings::get('supported_locales', ['en']);
        foreach ($supportedLocales as $locale) {
            $altTexts[$locale] = '';
        }

        $media = Media::create([
            'filename' => $this->file->getClientOriginalName(),
            'filepath' => $filepath,
            'file_type' => $fileType,
            'file_size' => $this->file->getSize(),
            'alt_text' => $altTexts,
        ]);

        ActivityLogger::log('media_uploaded', "Uploaded media file: {$media->filename}");

        $this->file = null;
        session()->flash('message', 'Media file uploaded successfully.');
    }

    public function selectMedia(int $id)
    {
        $media = Media::findOrFail($id);
        $this->selectedMediaId = $id;
        
        $supportedLocales = \App\Services\SiteSettings::get('supported_locales', ['en']);
        
        $this->alt_text = [];
        foreach ($supportedLocales as $locale) {
            $this->alt_text[$locale] = $media->alt_text[$locale] ?? '';
        }
    }

    public function saveAltText()
    {
        if (!$this->selectedMediaId) {
            return;
        }

        $media = Media::findOrFail($this->selectedMediaId);
        $media->update([
            'alt_text' => $this->alt_text,
        ]);

        ActivityLogger::log('media_updated', "Updated alt texts for: {$media->filename}");
        $this->selectedMediaId = null;
        session()->flash('message', 'Alt text saved successfully.');
    }

    public function deleteMedia(int $id)
    {
        $media = Media::findOrFail($id);
        
        // Remove file from disk
        \Illuminate\Support\Facades\Storage::disk('public')->delete($media->filepath);
        
        $media->delete();
        ActivityLogger::log('media_deleted', "Deleted media file: {$media->filename}");
        
        if ($this->selectedMediaId === $id) {
            $this->selectedMediaId = null;
        }

        session()->flash('message', 'Media file deleted successfully.');
    }

    public function render()
    {
        $mediaItems = Media::where('filename', 'like', "%{$this->search}%")
            ->orderBy('id', 'desc')
            ->get();

        $supportedLocales = \App\Services\SiteSettings::get('supported_locales', ['en']);

        return view('livewire.admin.media-manager', compact('mediaItems', 'supportedLocales'))
            ->layout('components.layouts.admin');
    }
}
