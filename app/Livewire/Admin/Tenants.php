<?php

namespace App\Livewire\Admin;

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Tenants extends Component
{
    // Tenant Fields
    public string $name = '';
    public string $subdomain = '';
    public string $default_locale = 'en';

    // Tenant Admin User Fields
    public string $admin_name = '';
    public string $admin_email = '';
    public string $admin_password = '';

    // Domain Mapping Fields
    public ?int $selected_tenant_id = null;
    public string $new_domain = '';

    // Mode
    public bool $isCreating = false;
    public bool $isMappingDomain = false;

    protected function rules(): array
    {
        if ($this->isMappingDomain) {
            return [
                'new_domain' => 'required|string|unique:tenant_domains,domain',
                'selected_tenant_id' => 'required|exists:tenants,id',
            ];
        }

        return [
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|alpha_dash|unique:tenants,subdomain',
            'default_locale' => 'required|string|max:5',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:6',
        ];
    }

    public function toggleCreate()
    {
        $this->isCreating = !$this->isCreating;
        $this->isMappingDomain = false;
        $this->resetInputFields();
    }

    public function toggleMapDomain(?int $tenantId = null)
    {
        $this->selected_tenant_id = $tenantId;
        $this->isMappingDomain = !$this->isMappingDomain;
        $this->isCreating = false;
        $this->new_domain = '';
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->subdomain = '';
        $this->default_locale = 'en';
        $this->admin_name = '';
        $this->admin_email = '';
        $this->admin_password = '';
        $this->new_domain = '';
        $this->selected_tenant_id = null;
    }

    public function saveTenant()
    {
        $this->isMappingDomain = false;
        $this->validate();

        // 1. Create Tenant
        $tenant = Tenant::create([
            'name' => $this->name,
            'subdomain' => $this->subdomain,
            'default_locale' => $this->default_locale,
            'supported_locales' => [$this->default_locale],
            'settings' => [
                'meta_description' => 'Welcome to ' . $this->name,
                'logo' => '',
                'adsense_client_id' => '',
            ],
        ]);

        // 2. Create Tenant Admin user
        // We temporarily turn off the global tenant scope on User to write a user mapped to the tenant
        User::withoutGlobalScopes()->create([
            'tenant_id' => $tenant->id,
            'name' => $this->admin_name,
            'email' => $this->admin_email,
            'password' => Hash::make($this->admin_password),
            'role' => 'tenant_admin',
        ]);

        // 3. Create default subdomain mapping (e.g. site1.localhost)
        // Map subdomain under localhost or central domain for development
        $centralHost = 'localhost'; // Fallback
        $mappedDomain = $this->subdomain . '.' . $centralHost;
        TenantDomain::create([
            'tenant_id' => $tenant->id,
            'domain' => $mappedDomain,
            'is_primary' => true,
        ]);

        $this->isCreating = false;
        $this->resetInputFields();
        session()->flash('message', 'Website created successfully with primary subdomain: ' . $mappedDomain);
    }

    public function mapDomain()
    {
        $this->isMappingDomain = true;
        $this->validate();

        TenantDomain::create([
            'tenant_id' => $this->selected_tenant_id,
            'domain' => $this->new_domain,
            'is_primary' => false,
        ]);

        $this->isMappingDomain = false;
        $this->resetInputFields();
        session()->flash('message', 'Custom domain mapping added successfully.');
    }

    public function toggleTenantStatus(int $id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->is_active = !$tenant->is_active;
        $tenant->save();
    }

    public function deleteTenant(int $id)
    {
        $tenant = Tenant::findOrFail($id);
        
        // Remove all cache mappings for domains
        $tenant->domains->each(fn($d) => app(\App\Services\TenantManager::class)->clearCache($d->domain));
        
        // Also clear subdomain cache
        app(\App\Services\TenantManager::class)->clearCache($tenant->subdomain . '.localhost');

        $tenant->delete();
        session()->flash('message', 'Website and all associated content deleted successfully.');
    }

    public function render()
    {
        // Use withoutGlobalScopes to query across all tenants (specifically for Super Admin)
        $tenants = Tenant::with('domains')->orderBy('id', 'desc')->get();

        return view('livewire.admin.tenants', compact('tenants'))
            ->layout('components.layouts.admin');
    }
}
