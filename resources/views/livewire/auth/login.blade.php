<div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-8 rounded-2xl shadow-2xl relative">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold tracking-tight text-white bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-pink-500">
            VK SaaS CMS
        </h2>
        <p class="text-sm text-slate-400 mt-2">Sign in to your administration dashboard</p>
    </div>

    <form wire:submit.prevent="login" class="space-y-6">
        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Email Address</label>
            <input wire:model="email" type="email" id="email" 
                class="mt-1.5 block w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200" 
                placeholder="admin@example.com" required autocomplete="email">
            @error('email') 
                <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> 
            @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Password</label>
            <input wire:model="password" type="password" id="password" 
                class="mt-1.5 block w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200" 
                placeholder="••••••••" required autocomplete="current-password">
            @error('password') 
                <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> 
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center cursor-pointer select-none">
                <input wire:model="remember" type="checkbox" class="sr-only peer">
                <div class="w-5 h-5 bg-slate-950 border border-slate-800 rounded flex items-center justify-center peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all duration-200 mr-2">
                    <svg class="w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="text-sm text-slate-400">Remember me</span>
            </label>
        </div>

        <button type="submit" 
            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg shadow-indigo-600/20 active:scale-[0.98]">
            <span wire:loading.remove wire:target="login">Sign In</span>
            <span wire:loading wire:target="login" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
            </span>
        </button>
    </form>
</div>
