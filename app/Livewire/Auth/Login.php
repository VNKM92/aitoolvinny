<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    // 2FA Properties
    public bool $showTwoFactorForm = false;
    public string $twoFactorCodeInput = '';
    public ?int $tempUserId = null;

    protected array $rules = [
        'email' => 'required|email',
        'password' => 'required|string|min:6',
    ];

    /**
     * Handle login credentials verification.
     */
    public function login()
    {
        $this->validate();

        $throttleKey = strtolower($this->email) . '|' . request()->ip();

        // 1. Rate Limiting Check (max 5 login attempts per minute)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Too many login attempts. Please try again in {$seconds} seconds.");
            return;
        }

        // 2. Authenticate credentials
        if (Auth::validate(['email' => $this->email, 'password' => $this->password])) {
            $user = User::where('email', $this->email)->first();

            // Clear throttle on correct credentials check (before 2FA verification)
            RateLimiter::clear($throttleKey);

            // 3. Two-Factor Authentication Check
            if ($user->two_factor_enabled) {
                // Generate 2FA code
                $code = (string) rand(100000, 999999);
                $user->update([
                    'two_factor_code' => $code,
                    'two_factor_expires_at' => now()->addMinutes(15),
                ]);

                // Store in session and local properties
                $this->tempUserId = $user->id;
                $this->showTwoFactorForm = true;

                // Log the 2FA code for easy retrieval in local environment testing
                Log::info("2FA Login Code for User {$user->email}: {$code}");
                session()->flash('two_factor_message', "Your 2FA code is: {$code} (logged in storage/logs/laravel.log)");

                // Log login history as pending 2FA verification
                LoginHistory::create([
                    'user_id' => $user->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'status' => 'pending_2fa',
                ]);

                return;
            }

            // Direct login if 2FA is not enabled
            Auth::login($user, $this->remember);
            session()->regenerate();

            // Log successful login
            LoginHistory::create([
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'success',
            ]);

            return redirect()->route('admin.dashboard');
        }

        // Hit rate limiter on failed attempt
        RateLimiter::hit($throttleKey, 60);

        // Log failed login attempt
        $failedUser = User::where('email', $this->email)->first();
        LoginHistory::create([
            'user_id' => $failedUser?->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'failed',
        ]);

        $this->addError('email', 'The provided credentials do not match our records.');
    }

    /**
     * Handle verification of the 2FA code.
     */
    public function verifyTwoFactor()
    {
        $this->validate([
            'twoFactorCodeInput' => 'required|string|size:6',
        ]);

        $user = User::findOrFail($this->tempUserId);

        // Verify code match and code expiry
        if ($user->two_factor_code === $this->twoFactorCodeInput && $user->two_factor_expires_at->isFuture()) {
            // Reset code
            $user->update([
                'two_factor_code' => null,
                'two_factor_expires_at' => null,
            ]);

            Auth::login($user, $this->remember);
            session()->regenerate();

            // Log successful 2FA login
            LoginHistory::create([
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'success',
            ]);

            return redirect()->route('admin.dashboard');
        }

        // Log failed 2FA verification attempt
        LoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'failed_2fa',
        ]);

        $this->addError('twoFactorCodeInput', 'The verification code is invalid or has expired.');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.auth');
    }
}
