<?php

namespace App\Livewire\Admin;

use App\Models\Tenant;
use App\Services\TenantManager;
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
        $tenant = app(TenantManager::class)->getTenant();
        
        if (!$tenant) {
            return redirect()->route('admin.dashboard');
        }

        $this->siteName = $tenant->name;
        $this->defaultLocale = $tenant->default_locale;
        $this->supportedLocales = $tenant->supported_locales ?? [$tenant->default_locale];

        $settings = $tenant->settings;
        $this->metaDescription = $settings['meta_description'] ?? '';
        $this->existingLogo = $settings['logo'] ?? '';
        $this->adsenseClientId = $settings['adsense_client_id'] ?? '';
        $this->adsenseTopSlot = $settings['adsense_top_slot'] ?? '';
        $this->adsenseSidebarSlot = $settings['adsense_sidebar_slot'] ?? '';
        $this->adsenseArticleSlot = $settings['adsense_article_slot'] ?? '';
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

        $tenant = app(TenantManager::class)->getTenant();

        // 1. Prepare settings array
        $settings = $tenant->settings ?? [];
        $settings['meta_description'] = $this->metaDescription;
        $settings['adsense_client_id'] = $this->adsenseClientId;
        $settings['adsense_top_slot'] = $this->adsenseTopSlot;
        $settings['adsense_sidebar_slot'] = $this->adsenseSidebarSlot;
        $settings['adsense_article_slot'] = $this->adsenseArticleSlot;

        // 2. Handle Logo Upload
        if ($this->logo) {
            $logoPath = $this->logo->store('branding', 'public');
            $settings['logo'] = asset('storage/' . $logoPath);
            $this->existingLogo = $settings['logo'];
        }

        // 3. Update Tenant
        $tenant->update([
            'name' => $this->siteName,
            'default_locale' => $this->defaultLocale,
            'supported_locales' => array_values($this->supportedLocales),
            'settings' => $settings,
        ]);

        // Clear tenancy domain caches
        $tenant->domains->each(fn($d) => app(TenantManager::class)->clearCache($d->domain));
        
        session()->flash('message', 'Settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings')
            ->layout('components.layouts.admin');
    }
}
