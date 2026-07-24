<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\User;
use App\Services\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenancyTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenantA;
    protected Tenant $tenantB;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Tenant A
        $this->tenantA = Tenant::create([
            'name' => 'Tenant A',
            'subdomain' => 'tenant-a',
            'default_locale' => 'en',
            'supported_locales' => ['en', 'es'],
            'settings' => [],
        ]);

        TenantDomain::create([
            'tenant_id' => $this->tenantA->id,
            'domain' => 'tenant-a.test',
            'is_primary' => true,
        ]);

        // Create Tenant B
        $this->tenantB = Tenant::create([
            'name' => 'Tenant B',
            'subdomain' => 'tenant-b',
            'default_locale' => 'es',
            'supported_locales' => ['es'],
            'settings' => [],
        ]);

        TenantDomain::create([
            'tenant_id' => $this->tenantB->id,
            'domain' => 'tenant-b.test',
            'is_primary' => true,
        ]);
    }

    /**
     * Test tenant resolution from hostname.
     */
    public function test_tenant_resolves_from_host()
    {
        $manager = app(TenantManager::class);

        // Resolve Tenant A
        $resolvedA = $manager->resolveFromHost('tenant-a.test');
        $this->assertNotNull($resolvedA);
        $this->assertEquals($this->tenantA->id, $resolvedA->id);

        // Resolve Tenant B
        $resolvedB = $manager->resolveFromHost('tenant-b.test');
        $this->assertNotNull($resolvedB);
        $this->assertEquals($this->tenantB->id, $resolvedB->id);
    }

    /**
     * Test database scoping and isolation between tenants.
     */
    public function test_tenant_data_is_isolated()
    {
        $manager = app(TenantManager::class);

        // Set context to Tenant A and create a category
        $manager->setTenant($this->tenantA);
        $categoryA = Category::create([
            'name' => ['en' => 'Category A'],
            'slug' => 'cat-a',
        ]);

        // Set context to Tenant B and create a category
        $manager->setTenant($this->tenantB);
        $categoryB = Category::create([
            'name' => ['es' => 'Category B'],
            'slug' => 'cat-b',
        ]);

        // Check isolation
        $manager->setTenant($this->tenantA);
        $this->assertEquals(1, Category::count());
        $this->assertEquals('cat-a', Category::first()->slug);

        $manager->setTenant($this->tenantB);
        $this->assertEquals(1, Category::count());
        $this->assertEquals('cat-b', Category::first()->slug);
    }

    /**
     * Test locale routing and SetLocale middleware.
     */
    public function test_locale_middleware_sets_correct_locale()
    {
        $manager = app(TenantManager::class);

        // Set active tenant to Tenant A
        $manager->setTenant($this->tenantA);

        // Request without locale segment (should default to en)
        $this->get('http://tenant-a.test/')
            ->assertStatus(200);
        $this->assertEquals('en', app()->getLocale());

        // Request with es locale segment (should set to es)
        $response = $this->get('http://tenant-a.test/es');
        if ($response->status() !== 200) {
            dump($response->getContent());
        }
        $response->assertStatus(200);
        $this->assertEquals('es', app()->getLocale());
    }
}
