<?php

use Livewire\Component;
use Livewire\Attributes\Rule;
use App\Models\Subscriber;

new class extends Component
{
    #[Rule('required|email|max:255')]
    public string $email = '';

    public function subscribe()
    {
        $this->validate();

        $exists = Subscriber::where('email', $this->email)->exists();

        if (!$exists) {
            Subscriber::create([
                'email' => $this->email,
                'is_active' => true,
            ]);
        }

        $this->email = '';
        session()->flash('newsletter_message', 'Thank you for subscribing!');
    }
};
?>

<div class="backdrop-blur-md bg-slate-900/50 border border-slate-800 p-6 rounded-2xl">
    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-2">Subscribe to Newsletter</h4>
    <p class="text-xs text-slate-400 mb-4">Receive the latest blog posts and announcements straight to your inbox.</p>
    
    @if(session()->has('newsletter_message'))
        <div class="p-3 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-lg text-xs font-semibold">
            {{ session('newsletter_message') }}
        </div>
    @else
        <form wire:submit.prevent="subscribe" class="space-y-2">
            <input wire:model="email" type="email" 
                class="block w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                placeholder="you@example.com" required>
            @error('email') <span class="text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
            
            <button type="submit" 
                class="w-full py-2 bg-indigo-650 hover:bg-indigo-550 rounded-lg text-xs font-semibold text-white transition-colors">
                Subscribe Now
            </button>
        </form>
    @endif
</div>