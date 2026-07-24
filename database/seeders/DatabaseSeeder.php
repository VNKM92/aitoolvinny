<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Super Admin User
        User::withoutGlobalScopes()->create([
            'name' => 'SaaS Super Admin',
            'email' => 'admin@saas.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'tenant_id' => null,
        ]);

        // 2. Create default Tenant website
        $tenant = Tenant::create([
            'name' => 'Sample Dev Blog',
            'subdomain' => 'devblog',
            'default_locale' => 'en',
            'supported_locales' => ['en', 'es'],
            'settings' => [
                'meta_description' => 'The ultimate news platform for developers.',
                'logo' => '',
                'adsense_client_id' => '',
            ],
        ]);

        // 3. Create Tenant Domain Mapping
        TenantDomain::create([
            'tenant_id' => $tenant->id,
            'domain' => 'devblog.localhost',
            'is_primary' => true,
        ]);

        // 4. Create Tenant Admin User
        User::withoutGlobalScopes()->create([
            'name' => 'Dev Blog Editor',
            'email' => 'editor@devblog.com',
            'password' => Hash::make('password'),
            'role' => 'tenant_admin',
            'tenant_id' => $tenant->id,
        ]);
    }
}
