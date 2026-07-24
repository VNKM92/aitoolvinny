<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Subscriber;
use Livewire\Component;

class Dashboard extends Component
{
    public int $totalPosts = 0;
    public int $totalCategories = 0;
    public int $totalPages = 0;
    public int $totalComments = 0;
    public int $totalSubscribers = 0;

    public function mount()
    {
        $this->totalPosts = Post::count();
        $this->totalCategories = Category::count();
        $this->totalPages = Page::count();
        $this->totalComments = Comment::count();
        $this->totalSubscribers = Subscriber::count();
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('components.layouts.admin');
    }
}
