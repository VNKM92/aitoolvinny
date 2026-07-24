<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\SiteSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Unified Administrator User
        User::create([
            'name' => 'CMS Administrator',
            'email' => 'admin@cms.com',
            'password' => Hash::make('password'),
            'role' => 'administrator',
        ]);

        // 2. Seed Default Site-Wide Settings
        SiteSettings::set('site_name', 'Sample Dev Blog');
        SiteSettings::set('default_locale', 'en');
        SiteSettings::set('supported_locales', ['en', 'es']);
        SiteSettings::set('meta_description', 'The ultimate news platform for developers.');
        SiteSettings::set('logo', '');
        SiteSettings::set('adsense_client_id', '');
        SiteSettings::set('adsense_top_slot', '');
        SiteSettings::set('adsense_sidebar_slot', '');
        SiteSettings::set('adsense_article_slot', '');

        // Seed default theme colors and unified theme objects.
        SiteSettings::set('theme_settings', [
            'theme_primary' => '#4f46e5',
            'theme_primary_hover' => '#4338ca',
            'theme_header_bg' => '#020617',
            'theme_footer_bg' => '#020617',
        ]);

        SiteSettings::set('admin_theme_settings', [
            'theme_backend_primary' => '#6366f1',
            'theme_backend_primary_hover' => '#4f46e5',
        ]);

        SiteSettings::set('theme_primary', '#4f46e5');
        SiteSettings::set('theme_primary_hover', '#4338ca');
        SiteSettings::set('theme_header_bg', '#020617');
        SiteSettings::set('theme_footer_bg', '#020617');
        SiteSettings::set('theme_backend_primary', '#6366f1');
        SiteSettings::set('theme_backend_primary_hover', '#4f46e5');
    }
}
