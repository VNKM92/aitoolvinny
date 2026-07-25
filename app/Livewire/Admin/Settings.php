<?php

namespace App\Livewire\Admin;

use App\Services\SiteSettings;
use App\Services\ActivityLogger;
use App\Services\ThemeService;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    public string $siteName = '';
    public string $metaDescription = '';
    public $logo;
    public ?string $existingLogo = null;

    public string $adsenseClientId = '';
    public string $adsenseTopSlot = '';
    public string $adsenseSidebarSlot = '';
    public string $adsenseArticleSlot = '';

    public string $defaultLocale = 'en';
    public array $supportedLocales = [];
    public string $newLocale = '';

    public string $selectedPreset = '';

    // Comprehensive Frontend Body/Theme
    public string $themePrimary = '#4f46e5';
    public string $themePrimaryHover = '#4338ca';
    public string $themeSecondary = '#64748b';
    public string $themeAccent = '#ec4899';

    public string $themeBodyBg = '#f8fafc';
    public string $themeBodyBgAlt = '#f1f5f9';
    public string $themeBodyText = '#0f172a';
    public string $themeBodyHeadingColor = '#0f172a';
    public string $themeBodyLinkColor = '#2563eb';
    public string $themeBodyLinkHover = '#1d4ed8';

    public string $themeSurfaceBg = '#ffffff';
    public string $themeCardBg = 'rgba(255, 255, 255, 0.95)';
    public string $themeSectionBg = '#f1f5f9';
    public string $themeHeaderBg = 'rgba(255, 255, 255, 0.96)';
    public string $themeHeaderText = '#0f172a';
    public string $themeFooterBg = '#0f172a';
    public string $themeFooterText = '#cbd5e1';

    public string $themeSidebarBg = '#ffffff';
    public string $themeSidebarActive = '#4f46e5';
    public string $themeNavColor = '#334155';
    public string $themeNavHover = '#4f46e5';
    public string $themeBorderColor = 'rgba(15, 23, 42, 0.08)';

    // Dark Mode overrides
    public string $themeDarkMode = 'auto';
    public string $themeDarkBodyBg = '#020617';
    public string $themeDarkBodyText = '#e2e8f0';
    public string $themeDarkSurfaceBg = '#0f172a';
    public string $themeDarkCardBg = '#111827';

    // Typography
    public string $themeFontHeading = "'Playfair Display', 'Outfit', sans-serif";
    public string $themeFontBody = "'Inter', sans-serif";
    public string $themeFontSizeBase = '16px';
    public string $themeLineHeightBase = '1.65';

    // Buttons/Cards styling
    public string $themeButtonRadius = '0.5rem';
    public string $themeCardRadius = '0.75rem';
    public string $themeCardShadow = '0 1px 3px rgba(15, 23, 42, 0.06)';
    public string $themeCardHoverShadow = '0 10px 25px rgba(15, 23, 42, 0.08)';

    // Forms
    public string $themeFormInputBg = '#ffffff';
    public string $themeFormInputBorder = '#e2e8f0';
    public string $themeFormPlaceholder = '#94a3b8';
    public string $themeFormFocusBorder = '#4f46e5';
    public string $themeFormLabel = '#334155';
    public string $themeFormRadius = '0.5rem';

    // Admin Backend Theme
    public string $themeBackendPrimary = '#6366f1';
    public string $themeBackendPrimaryHover = '#4f46e5';
    public string $themeAdminBodyBg = '#f1f5f9';
    public string $themeAdminBodyText = '#0f172a';
    public string $themeAdminSidebarBg = '#0f172a';
    public string $themeAdminSidebarText = '#cbd5e1';
    public string $themeAdminSidebarActive = '#4f46e5';
    public string $themeAdminCardsBg = '#ffffff';
    public string $themeAdminFormsBg = '#f8fafc';

    protected function rules(): array
    {
        return [
            'siteName' => 'required|string|max:255',
            'metaDescription' => 'nullable|string|max:500',
            'logo' => 'nullable|image|max:1024',
            'adsenseClientId' => 'nullable|string|max:100',
            'adsenseTopSlot' => 'nullable|string|max:100',
            'adsenseSidebarSlot' => 'nullable|string|max:100',
            'adsenseArticleSlot' => 'nullable|string|max:100',
            'defaultLocale' => 'required|string|max:5',
            'themePrimary' => 'required|string',
            'themePrimaryHover' => 'required|string',
            'themeSecondary' => 'required|string',
            'themeAccent' => 'required|string',
            'themeBodyBg' => 'required|string',
            'themeBodyBgAlt' => 'required|string',
            'themeBodyText' => 'required|string',
            'themeBodyHeadingColor' => 'required|string',
            'themeBodyLinkColor' => 'required|string',
            'themeBodyLinkHover' => 'required|string',
            'themeSurfaceBg' => 'required|string',
            'themeCardBg' => 'required|string',
            'themeSectionBg' => 'required|string',
            'themeHeaderBg' => 'required|string',
            'themeHeaderText' => 'required|string',
            'themeFooterBg' => 'required|string',
            'themeFooterText' => 'required|string',
            'themeSidebarBg' => 'required|string',
            'themeSidebarActive' => 'required|string',
            'themeNavColor' => 'required|string',
            'themeNavHover' => 'required|string',
            'themeBorderColor' => 'required|string',
            'themeDarkMode' => 'required|string|in:auto,light,dark',
            'themeDarkBodyBg' => 'required|string',
            'themeDarkBodyText' => 'required|string',
            'themeDarkSurfaceBg' => 'required|string',
            'themeDarkCardBg' => 'required|string',
            'themeFontHeading' => 'required|string|max:255',
            'themeFontBody' => 'required|string|max:255',
            'themeFontSizeBase' => 'required|string|max:20',
            'themeLineHeightBase' => 'required|string|max:20',
            'themeButtonRadius' => 'required|string|max:30',
            'themeCardRadius' => 'required|string|max:30',
            'themeCardShadow' => 'required|string|max:255',
            'themeCardHoverShadow' => 'required|string|max:255',
            'themeFormInputBg' => 'required|string',
            'themeFormInputBorder' => 'required|string',
            'themeFormPlaceholder' => 'required|string',
            'themeFormFocusBorder' => 'required|string',
            'themeFormLabel' => 'required|string',
            'themeFormRadius' => 'required|string|max:30',
            'themeBackendPrimary' => 'required|string',
            'themeBackendPrimaryHover' => 'required|string',
            'themeAdminBodyBg' => 'required|string',
            'themeAdminBodyText' => 'required|string',
            'themeAdminSidebarBg' => 'required|string',
            'themeAdminSidebarText' => 'required|string',
            'themeAdminSidebarActive' => 'required|string',
            'themeAdminCardsBg' => 'required|string',
            'themeAdminFormsBg' => 'required|string',
        ];
    }

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

        $themeSettings = ThemeService::themeSettings();
        $adminThemeSettings = ThemeService::adminThemeSettings();

        $map = [
            'themePrimary' => 'theme_primary',
            'themePrimaryHover' => 'theme_primary_hover',
            'themeSecondary' => 'theme_secondary',
            'themeAccent' => 'theme_accent',
            'themeBodyBg' => 'theme_body_bg',
            'themeBodyBgAlt' => 'theme_body_bg_alt',
            'themeBodyText' => 'theme_body_text',
            'themeBodyHeadingColor' => 'theme_body_heading_color',
            'themeBodyLinkColor' => 'theme_body_link_color',
            'themeBodyLinkHover' => 'theme_body_link_hover',
            'themeSurfaceBg' => 'theme_surface_bg',
            'themeCardBg' => 'theme_card_bg',
            'themeSectionBg' => 'theme_section_bg',
            'themeHeaderBg' => 'theme_header_bg',
            'themeHeaderText' => 'theme_header_text',
            'themeFooterBg' => 'theme_footer_bg',
            'themeFooterText' => 'theme_footer_text',
            'themeSidebarBg' => 'theme_sidebar_bg',
            'themeSidebarActive' => 'theme_sidebar_active',
            'themeNavColor' => 'theme_nav_color',
            'themeNavHover' => 'theme_nav_hover',
            'themeBorderColor' => 'theme_border_color',
            'themeDarkMode' => 'theme_dark_mode',
            'themeDarkBodyBg' => 'theme_dark_body_bg',
            'themeDarkBodyText' => 'theme_dark_body_text',
            'themeDarkSurfaceBg' => 'theme_dark_surface_bg',
            'themeDarkCardBg' => 'theme_dark_card_bg',
            'themeFontHeading' => 'theme_font_heading',
            'themeFontBody' => 'theme_font_body',
            'themeFontSizeBase' => 'theme_font_size_base',
            'themeLineHeightBase' => 'theme_line_height_base',
            'themeButtonRadius' => 'theme_button_radius',
            'themeCardRadius' => 'theme_card_radius',
            'themeCardShadow' => 'theme_card_shadow',
            'themeCardHoverShadow' => 'theme_card_hover_shadow',
            'themeFormInputBg' => 'theme_form_input_bg',
            'themeFormInputBorder' => 'theme_form_input_border',
            'themeFormPlaceholder' => 'theme_form_placeholder',
            'themeFormFocusBorder' => 'theme_form_focus_border',
            'themeFormLabel' => 'theme_form_label',
            'themeFormRadius' => 'theme_form_radius',
        ];

        $defaults = ThemeService::defaults();
        foreach ($map as $prop => $key) {
            $this->$prop = $themeSettings[$key] ?? $defaults[$key] ?? $this->$prop;
        }

        $adminMap = [
            'themeBackendPrimary' => 'theme_backend_primary',
            'themeBackendPrimaryHover' => 'theme_backend_primary_hover',
            'themeAdminBodyBg' => 'theme_admin_body_bg',
            'themeAdminBodyText' => 'theme_admin_body_text',
            'themeAdminSidebarBg' => 'theme_admin_sidebar_bg',
            'themeAdminSidebarText' => 'theme_admin_sidebar_text',
            'themeAdminSidebarActive' => 'theme_admin_sidebar_active',
            'themeAdminCardsBg' => 'theme_admin_cards_bg',
            'themeAdminFormsBg' => 'theme_admin_forms_bg',
        ];

        foreach ($adminMap as $prop => $key) {
            $this->$prop = $adminThemeSettings[$key] ?? $defaults[$key] ?? $this->$prop;
        }
    }

    public function updatedSelectedPreset($value)
    {
        if (empty($value)) {
            return;
        }

        $presets = ThemeService::presets();
        if (isset($presets[$value])) {
            $values = $presets[$value]['values'];

            $map = [
                'themePrimary' => 'theme_primary',
                'themePrimaryHover' => 'theme_primary_hover',
                'themeSecondary' => 'theme_secondary',
                'themeAccent' => 'theme_accent',
                'themeBodyBg' => 'theme_body_bg',
                'themeBodyBgAlt' => 'theme_body_bg_alt',
                'themeBodyText' => 'theme_body_text',
                'themeBodyHeadingColor' => 'theme_body_heading_color',
                'themeBodyLinkColor' => 'theme_body_link_color',
                'themeBodyLinkHover' => 'theme_body_link_hover',
                'themeSurfaceBg' => 'theme_surface_bg',
                'themeCardBg' => 'theme_card_bg',
                'themeSectionBg' => 'theme_section_bg',
                'themeHeaderBg' => 'theme_header_bg',
                'themeHeaderText' => 'theme_header_text',
                'themeFooterBg' => 'theme_footer_bg',
                'themeFooterText' => 'theme_footer_text',
                'themeSidebarBg' => 'theme_sidebar_bg',
                'themeSidebarActive' => 'theme_sidebar_active',
                'themeNavColor' => 'theme_nav_color',
                'themeNavHover' => 'theme_nav_hover',
                'themeBorderColor' => 'theme_border_color',
                'themeDarkMode' => 'theme_dark_mode',
                'themeDarkBodyBg' => 'theme_dark_body_bg',
                'themeDarkBodyText' => 'theme_dark_body_text',
                'themeDarkSurfaceBg' => 'theme_dark_surface_bg',
                'themeDarkCardBg' => 'theme_dark_card_bg',
                'themeFontHeading' => 'theme_font_heading',
                'themeFontBody' => 'theme_font_body',
                'themeFontSizeBase' => 'theme_font_size_base',
                'themeLineHeightBase' => 'theme_line_height_base',
                'themeButtonRadius' => 'theme_button_radius',
                'themeCardRadius' => 'theme_card_radius',
                'themeCardShadow' => 'theme_card_shadow',
                'themeCardHoverShadow' => 'theme_card_hover_shadow',
                'themeFormInputBg' => 'theme_form_input_bg',
                'themeFormInputBorder' => 'theme_form_input_border',
                'themeFormPlaceholder' => 'theme_form_placeholder',
                'themeFormFocusBorder' => 'theme_form_focus_border',
                'themeFormLabel' => 'theme_form_label',
                'themeFormRadius' => 'theme_form_radius',
                'themeBackendPrimary' => 'theme_backend_primary',
                'themeBackendPrimaryHover' => 'theme_backend_primary_hover',
                'themeAdminBodyBg' => 'theme_admin_body_bg',
                'themeAdminBodyText' => 'theme_admin_body_text',
                'themeAdminSidebarBg' => 'theme_admin_sidebar_bg',
                'themeAdminSidebarText' => 'theme_admin_sidebar_text',
                'themeAdminSidebarActive' => 'theme_admin_sidebar_active',
                'themeAdminCardsBg' => 'theme_admin_cards_bg',
                'themeAdminFormsBg' => 'theme_admin_forms_bg',
            ];

            foreach ($map as $prop => $key) {
                if (isset($values[$key])) {
                    $this->$prop = $values[$key];
                }
            }

            session()->flash('message', "Preset '{$presets[$value]['name']}' loaded. Click Save to apply permanently.");
        }

        $this->selectedPreset = '';
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

        SiteSettings::set('site_name', $this->siteName);
        SiteSettings::set('default_locale', $this->defaultLocale);
        SiteSettings::set('supported_locales', array_values($this->supportedLocales));
        SiteSettings::set('meta_description', $this->metaDescription);

        SiteSettings::set('adsense_client_id', $this->adsenseClientId);
        SiteSettings::set('adsense_top_slot', $this->adsenseTopSlot);
        SiteSettings::set('adsense_sidebar_slot', $this->adsenseSidebarSlot);
        SiteSettings::set('adsense_article_slot', $this->adsenseArticleSlot);

        $currentThemeSettings = SiteSettings::get('theme_settings', []);
        if (!is_array($currentThemeSettings)) {
            $currentThemeSettings = [];
        }

        $currentAdminThemeSettings = SiteSettings::get('admin_theme_settings', []);
        if (!is_array($currentAdminThemeSettings)) {
            $currentAdminThemeSettings = [];
        }

        $themeMap = [
            'theme_primary' => $this->themePrimary,
            'theme_primary_hover' => $this->themePrimaryHover,
            'theme_secondary' => $this->themeSecondary,
            'theme_accent' => $this->themeAccent,
            'theme_body_bg' => $this->themeBodyBg,
            'theme_body_bg_alt' => $this->themeBodyBgAlt,
            'theme_body_text' => $this->themeBodyText,
            'theme_body_heading_color' => $this->themeBodyHeadingColor,
            'theme_body_link_color' => $this->themeBodyLinkColor,
            'theme_body_link_hover' => $this->themeBodyLinkHover,
            'theme_surface_bg' => $this->themeSurfaceBg,
            'theme_card_bg' => $this->themeCardBg,
            'theme_section_bg' => $this->themeSectionBg,
            'theme_header_bg' => $this->themeHeaderBg,
            'theme_header_text' => $this->themeHeaderText,
            'theme_footer_bg' => $this->themeFooterBg,
            'theme_footer_text' => $this->themeFooterText,
            'theme_sidebar_bg' => $this->themeSidebarBg,
            'theme_sidebar_active' => $this->themeSidebarActive,
            'theme_nav_color' => $this->themeNavColor,
            'theme_nav_hover' => $this->themeNavHover,
            'theme_border_color' => $this->themeBorderColor,
            'theme_dark_mode' => $this->themeDarkMode,
            'theme_dark_body_bg' => $this->themeDarkBodyBg,
            'theme_dark_body_text' => $this->themeDarkBodyText,
            'theme_dark_surface_bg' => $this->themeDarkSurfaceBg,
            'theme_dark_card_bg' => $this->themeDarkCardBg,
            'theme_font_heading' => $this->themeFontHeading,
            'theme_font_body' => $this->themeFontBody,
            'theme_font_size_base' => $this->themeFontSizeBase,
            'theme_line_height_base' => $this->themeLineHeightBase,
            'theme_button_radius' => $this->themeButtonRadius,
            'theme_card_radius' => $this->themeCardRadius,
            'theme_card_shadow' => $this->themeCardShadow,
            'theme_card_hover_shadow' => $this->themeCardHoverShadow,
            'theme_form_input_bg' => $this->themeFormInputBg,
            'theme_form_input_border' => $this->themeFormInputBorder,
            'theme_form_placeholder' => $this->themeFormPlaceholder,
            'theme_form_focus_border' => $this->themeFormFocusBorder,
            'theme_form_label' => $this->themeFormLabel,
            'theme_form_radius' => $this->themeFormRadius,
        ];

        $adminMap = [
            'theme_backend_primary' => $this->themeBackendPrimary,
            'theme_backend_primary_hover' => $this->themeBackendPrimaryHover,
            'theme_admin_body_bg' => $this->themeAdminBodyBg,
            'theme_admin_body_text' => $this->themeAdminBodyText,
            'theme_admin_sidebar_bg' => $this->themeAdminSidebarBg,
            'theme_admin_sidebar_text' => $this->themeAdminSidebarText,
            'theme_admin_sidebar_active' => $this->themeAdminSidebarActive,
            'theme_admin_cards_bg' => $this->themeAdminCardsBg,
            'theme_admin_forms_bg' => $this->themeAdminFormsBg,
        ];

        $updatedThemeSettings = array_merge($currentThemeSettings, $themeMap);
        $updatedAdminThemeSettings = array_merge($currentAdminThemeSettings, $adminMap);

        SiteSettings::set('theme_settings', $updatedThemeSettings);
        SiteSettings::set('admin_theme_settings', $updatedAdminThemeSettings);

        foreach ($themeMap as $k => $v) {
            SiteSettings::set($k, $v);
        }
        foreach ($adminMap as $k => $v) {
            SiteSettings::set($k, $v);
        }

        if ($this->logo) {
            $logoPath = $this->logo->store('branding', 'public');
            $logoUrl = asset('storage/' . $logoPath);
            SiteSettings::set('logo', $logoUrl);
            $this->existingLogo = $logoUrl;
        }

        ActivityLogger::log('settings_updated', 'Updated site configuration, dynamic body colors, and theme settings');

        session()->flash('message', 'Settings updated successfully. Theme presets and dynamic body color are now active.');
    }

    public function render()
    {
        $presets = ThemeService::presets();
        return view('livewire.admin.settings', compact('presets'))
            ->layout('components.layouts.admin');
    }
}
