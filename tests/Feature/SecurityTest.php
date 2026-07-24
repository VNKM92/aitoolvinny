<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\LoginHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'CMS Admin',
            'email' => 'admin@cms.com',
            'password' => 'password',
            'role' => 'administrator',
            'two_factor_enabled' => false,
        ]);
    }

    /**
     * Test OWASP Security Headers are attached to web requests.
     */
    public function test_owasp_security_headers_are_present()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Content-Security-Policy');
    }

    /**
     * Test login history is logged on successful and failed login attempts.
     */
    public function test_login_attempts_log_history()
    {
        $this->assertEquals(0, LoginHistory::count());

        // 1. Test failed attempt
        Livewire::test(\App\Livewire\Auth\Login::class)
            ->set('email', 'admin@cms.com')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertEquals(1, LoginHistory::count());
        $this->assertEquals('failed', LoginHistory::first()->status);

        // 2. Test successful attempt
        Livewire::test(\App\Livewire\Auth\Login::class)
            ->set('email', 'admin@cms.com')
            ->set('password', 'password')
            ->call('login')
            ->assertHasNoErrors();

        $this->assertEquals(2, LoginHistory::count());
        $this->assertEquals('success', LoginHistory::orderBy('id', 'desc')->first()->status);
    }

    /**
     * Test 2FA authentication flow verification.
     */
    public function test_2fa_verification_flow()
    {
        // Enable 2FA on the admin
        $this->adminUser->update(['two_factor_enabled' => true]);

        // Submit correct credentials -> should prompt 2FA
        $loginTest = Livewire::test(\App\Livewire\Auth\Login::class)
            ->set('email', 'admin@cms.com')
            ->set('password', 'password')
            ->call('login')
            ->assertSet('showTwoFactorForm', true);

        // Verify database has code and pending 2FA history entry
        $this->adminUser->refresh();
        $this->assertNotNull($this->adminUser->two_factor_code);
        $this->assertDatabaseHas('login_histories', [
            'user_id' => $this->adminUser->id,
            'status' => 'pending_2fa'
        ]);

        // Test wrong 2FA code
        $loginTest->set('twoFactorCodeInput', '000000')
            ->call('verifyTwoFactor')
            ->assertHasErrors(['twoFactorCodeInput']);

        // Test correct 2FA code
        $loginTest->set('twoFactorCodeInput', $this->adminUser->two_factor_code)
            ->call('verifyTwoFactor')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('login_histories', [
            'user_id' => $this->adminUser->id,
            'status' => 'success'
        ]);
    }

    /**
     * Test activity logs single and bulk deletion operations.
     */
    public function test_activity_logs_deletion()
    {
        $log1 = ActivityLog::create([
            'user_id' => $this->adminUser->id,
            'action' => 'post_created',
            'description' => 'Created post 1'
        ]);

        $log2 = ActivityLog::create([
            'user_id' => $this->adminUser->id,
            'action' => 'post_created',
            'description' => 'Created post 2'
        ]);

        $this->assertEquals(2, ActivityLog::count());

        // Test single deletion
        Livewire::test(\App\Livewire\Admin\ActivityLogs::class)
            ->call('deleteLog', $log1->id);

        $this->assertEquals(1, ActivityLog::count());
        $this->assertDatabaseMissing('activity_logs', ['id' => $log1->id]);

        // Test bulk deletion
        $log3 = ActivityLog::create([
            'user_id' => $this->adminUser->id,
            'action' => 'post_deleted',
            'description' => 'Deleted post'
        ]);

        Livewire::test(\App\Livewire\Admin\ActivityLogs::class)
            ->set('selectedLogs', [$log2->id, $log3->id])
            ->call('deleteSelected');

        $this->assertEquals(0, ActivityLog::count());
    }
}
