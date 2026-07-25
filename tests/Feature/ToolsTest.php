<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TrafficTool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ToolsTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected TrafficTool $tool;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'CMS Admin',
            'email' => 'admin@cms.com',
            'password' => 'password',
            'role' => 'administrator',
        ]);

        $this->tool = TrafficTool::create([
            'slug' => 'qr-code-generator',
            'name' => ['en' => 'QR Code Generator'],
            'description' => ['en' => 'QR Code tag description'],
            'is_active' => true,
            'meta_title' => ['en' => 'QR SEO Title'],
            'meta_description' => ['en' => 'QR SEO Desc'],
        ]);
    }

    /**
     * Test admin tools manager requires auth and resolves.
     */
    public function test_admin_tools_manager_access()
    {
        $this->get('/admin/tools')->assertRedirect('/login');

        $response = $this->actingAs($this->adminUser)->get('/admin/tools');
        $response->assertStatus(200);
    }

    /**
     * Test public directory index and individual tool routes.
     */
    public function test_public_tools_endpoints()
    {
        // 1. Directory index
        $response = $this->get('/tools');
        $response->assertStatus(200);
        $response->assertSee('QR Code Generator');

        // 2. Individual tool
        $response = $this->get('/tools/qr-code-generator');
        $response->assertStatus(200);
        $response->assertSee('QR Code Generator');
        $response->assertSee('QR Code tag description');

        // 3. Inactive tool yields 404
        $this->tool->update(['is_active' => false]);
        $this->get('/tools/qr-code-generator')->assertStatus(404);
    }

    /**
     * Test Livewire admin settings modifications.
     */
    public function test_admin_updates_tool_configurations()
    {
        Livewire::actingAs($this->adminUser)
            ->test(\App\Livewire\Admin\ToolsManager::class)
            ->call('editTool', $this->tool->id)
            ->set('toolNameEn', 'Updated Generator')
            ->set('toolDescEn', 'New customized tagline')
            ->set('toolMetaTitleEn', 'New Meta Title')
            ->set('toolMetaDescEn', 'New Meta Desc')
            ->set('toolIsActive', true)
            ->call('saveTool')
            ->assertHasNoErrors();

        $this->tool->refresh();
        $this->assertEquals('Updated Generator', $this->tool->translate('name', 'en'));
        $this->assertEquals('New customized tagline', $this->tool->translate('description', 'en'));
        $this->assertEquals('New Meta Title', $this->tool->translate('meta_title', 'en'));
        $this->assertEquals('New Meta Desc', $this->tool->translate('meta_description', 'en'));
    }
}
