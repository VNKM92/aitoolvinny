<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantManager;
use Livewire\Component;

class Dashboard extends Component
{
    public int $totalTenants = 0;
    public int $totalUsers = 0;

    public int $totalPosts = 0;
    public int $totalCategories = 0;
    public int $totalPages = 0;

    public function mount()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $this->totalTenants = Tenant::count();
            // Count users excluding super admin
            $this->totalUsers = User::where('role', '!=', 'super_admin')->count();
        } else {
            // Under BelongsToTenant, these are automatically scoped to the active tenant!
            $this->totalPosts = Post::count();
            $this->totalCategories = Category::count();
            $this->totalPages = Page::count();
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('components.layouts.admin');
    }
}
