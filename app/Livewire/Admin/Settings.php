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

    // Dynamic Theme Settings
    public string $themePrimary = '#4f46e5';
    public string $themePrimaryHover = '#4338ca';
    public string $themeHeaderBg = '#020617';
    public string $themeFooterBg = '#020617';
    public string $themeBackendPrimary = '#6366f1';
    public string $themeBackendPrimaryHover = '#4f46e5';

    protected array $rules = [
        'siteName' => 'required|string|max:255',
        'metaDescription' => 'nullable|string|max:500',
        'logo' => 'nullable|image|max:1024',
        'adsenseClientId' => 'nullable|string|max:100',
        'adsenseTopSlot' => 'nullable|string|max:100',
        'adsenseSidebarSlot' => 'nullable|string|max:100',
        'adsenseArticleSlot' => 'nullable|string|max:100',
        'defaultLocale' => 'required|string|max:5',
        'themePrimary' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
        'themePrimaryHover' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
        'themeHeaderBg' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
        'themeFooterBg' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
        'themeBackendPrimary' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
        'themeBackendPrimaryHover' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
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

        // Load dynamic theme options from the unified theme settings service.
        $themeSettings = \App\Services\ThemeService::themeSettings();
        $adminThemeSettings = \App\Services\ThemeService::adminThemeSettings();

        $this->themePrimary = $themeSettings['theme_primary'] ?? '#4f46e5';
        $this->themePrimaryHover = $themeSettings['theme_primary_hover'] ?? '#4338ca';
        $this->themeHeaderBg = $themeSettings['theme_header_bg'] ?? '#020617';
        $this->themeFooterBg = $themeSettings['theme_footer_bg'] ?? '#020617';
        $this->themeBackendPrimary = $adminThemeSettings['theme_backend_primary'] ?? '#6366f1';
        $this->themeBackendPrimaryHover = $adminThemeSettings['theme_backend_primary_hover'] ?? '#4f46e5';
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

        // Save theme settings using unified settings objects and preserve legacy keys for backward compatibility.
        $currentThemeSettings = SiteSettings::get('theme_settings', []);
        if (!is_array($currentThemeSettings)) {
            $currentThemeSettings = [];
        }

        $currentAdminThemeSettings = SiteSettings::get('admin_theme_settings', []);
        if (!is_array($currentAdminThemeSettings)) {
            $currentAdminThemeSettings = [];
        }

        $updatedThemeSettings = array_merge($currentThemeSettings, [
            'theme_primary' => $this->themePrimary,
            'theme_primary_hover' => $this->themePrimaryHover,
            'theme_header_bg' => $this->themeHeaderBg,
            'theme_footer_bg' => $this->themeFooterBg,
        ]);

        $updatedAdminThemeSettings = array_merge($currentAdminThemeSettings, [
            'theme_backend_primary' => $this->themeBackendPrimary,
            'theme_backend_primary_hover' => $this->themeBackendPrimaryHover,
        ]);

        SiteSettings::set('theme_settings', $updatedThemeSettings);
        SiteSettings::set('admin_theme_settings', $updatedAdminThemeSettings);

        SiteSettings::set('theme_primary', $this->themePrimary);
        SiteSettings::set('theme_primary_hover', $this->themePrimaryHover);
        SiteSettings::set('theme_header_bg', $this->themeHeaderBg);
        SiteSettings::set('theme_footer_bg', $this->themeFooterBg);
        SiteSettings::set('theme_backend_primary', $this->themeBackendPrimary);
        SiteSettings::set('theme_backend_primary_hover', $this->themeBackendPrimaryHover);

        // 2. Handle Logo Upload
        if ($this->logo) {
            $logoPath = $this->logo->store('branding', 'public');
            $logoUrl = asset('storage/' . $logoPath);
            SiteSettings::set('logo', $logoUrl);
            $this->existingLogo = $logoUrl;
        }

        ActivityLogger::log('settings_updated', 'Updated site configuration and theme colors');
        
        session()->flash('message', 'Settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings')
            ->layout('components.layouts.admin');
    }
}
