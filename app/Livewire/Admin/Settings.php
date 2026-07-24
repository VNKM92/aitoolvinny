<?php

namespace App\Livewire\Admin;

use App\Services\SiteSettings;
use App\Services\ActivityLogger;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    // Site properties
    public string $siteName = '';
    public string $metaDescription = '';
    public $logo;
    public ?string $existingLogo = null;

    // Google AdSense settings
    public string $adsenseClientId = '';
    public string $adsenseTopSlot = '';
    public string $adsenseSidebarSlot = '';
    public string $adsenseArticleSlot = '';

    // Language settings
    public string $defaultLocale = 'en';
    public array $supportedLocales = [];
    public string $newLocale = '';

    protected array $rules = [
        'siteName' => 'required|string|max:255',
        'metaDescription' => 'nullable|string|max:500',
        'logo' => 'nullable|image|max:1024',
        'adsenseClientId' => 'nullable|string|max:100',
        'adsenseTopSlot' => 'nullable|string|max:100',
        'adsenseSidebarSlot' => 'nullable|string|max:100',
        'adsenseArticleSlot' => 'nullable|string|max:100',
        'defaultLocale' => 'required|string|max:5',
    ];

    public function mount()
    {
        $this->siteName = SiteSettings::get('site_name', 'My CMS Website');
        $this->defaultLocale = SiteSettings::get('default_locale', 'en');
        $this->supportedLocales = SiteSettings::get('supported_locales', ['en']);
        $this->metaDescription = SiteSettings::get('meta_description', '');
        $this->existingLogo = SiteSettings::get('logo', '');
        
        $this->adsenseClientId = SiteSettings::get('adsense_client_id', '');
        $this->adsenseTopSlot = SiteSettings::get('adsense_top_slot', '');
        $this->adsenseSidebarSlot = SiteSettings::get('adsense_sidebar_slot', '');
        $this->adsenseArticleSlot = SiteSettings::get('adsense_article_slot', '');
    }

    public function addLocale()
    {
        $this->newLocale = trim(strtolower($this->newLocale));

        if (empty($this->newLocale)) {
            return;
        }

        if (!in_array($this->newLocale, $this->supportedLocales)) {
            $this->supportedLocales[] = $this->newLocale;
        }

        $this->newLocale = '';
    }

    public function removeLocale(string $locale)
    {
        if ($locale === $this->defaultLocale) {
            session()->flash('error', 'Cannot remove the default site language.');
            return;
        }

        $this->supportedLocales = array_filter($this->supportedLocales, fn($l) => $l !== $locale);
    }

    public function saveSettings()
    {
        $this->validate();

        // 1. Save standard settings
        SiteSettings::set('site_name', $this->siteName);
        SiteSettings::set('default_locale', $this->defaultLocale);
        SiteSettings::set('supported_locales', array_values($this->supportedLocales));
        SiteSettings::set('meta_description', $this->metaDescription);
        
        SiteSettings::set('adsense_client_id', $this->adsenseClientId);
        SiteSettings::set('adsense_top_slot', $this->adsenseTopSlot);
        SiteSettings::set('adsense_sidebar_slot', $this->adsenseSidebarSlot);
        SiteSettings::set('adsense_article_slot', $this->adsenseArticleSlot);

        // 2. Handle Logo Upload
        if ($this->logo) {
            $logoPath = $this->logo->store('branding', 'public');
            $logoUrl = asset('storage/' . $logoPath);
            SiteSettings::set('logo', $logoUrl);
            $this->existingLogo = $logoUrl;
        }

        ActivityLogger::log('settings_updated', 'Updated site configuration preferences');
        
        session()->flash('message', 'Settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings')
            ->layout('components.layouts.admin');
    }
}
