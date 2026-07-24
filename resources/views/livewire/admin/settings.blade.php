<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-white">Settings</h1>
        <p class="text-slate-400 mt-1">Configure your website settings, branding, Google AdSense, and translations.</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-rose-950/20 border border-rose-900/30 text-rose-400 rounded-xl text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="saveSettings" class="space-y-8">
        <!-- Site Branding & Details -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl space-y-6">
            <h2 class="text-lg font-bold text-white border-b border-slate-800 pb-3">Website Branding</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Site Name</label>
                    <input wire:model="siteName" type="text" 
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white placeholder-slate-650 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="My Tech Website" required>
                    @error('siteName') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">SEO Default Meta Description</label>
                    <input wire:model="metaDescription" type="text" 
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white placeholder-slate-655 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="The ultimate news platform for developers.">
                    @error('metaDescription') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Logo Image</label>
                <input type="file" wire:model="logo" class="mt-1.5 block text-slate-400 text-xs">
                
                <div class="mt-3">
                    @if ($logo)
                        <span class="text-[10px] text-slate-500 block mb-1">New Logo Preview:</span>
                        <img src="{{ $logo->temporaryUrl() }}" class="h-12 w-auto bg-slate-950 p-2 border border-slate-800 rounded-lg">
                    @elseif ($existingLogo)
                        <span class="text-[10px] text-slate-500 block mb-1">Active Logo:</span>
                        <img src="{{ $existingLogo }}" class="h-12 w-auto bg-slate-950 p-2 border border-slate-800 rounded-lg">
                    @endif
                </div>
                @error('logo') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Multi-Language Management -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl space-y-6">
            <h2 class="text-lg font-bold text-white border-b border-slate-800 pb-3">Multi-Language & Localization</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Default Site Language</label>
                    <select wire:model="defaultLocale" 
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        @foreach($supportedLocales as $locale)
                            <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
                        @endforeach
                    </select>
                    @error('defaultLocale') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Add Language Code</label>
                    <div class="flex mt-1.5">
                        <input wire:model="newLocale" type="text" 
                            class="block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-l-lg text-white placeholder-slate-655 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                            placeholder="e.g. es, fr, de">
                        <button type="button" wire:click="addLocale" 
                            class="px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-r-lg text-sm font-semibold transition-colors">
                            Add
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Active Languages</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($supportedLocales as $locale)
                        <span class="inline-flex items-center px-3 py-1 bg-slate-950 text-indigo-400 border border-indigo-950 rounded-lg text-xs font-semibold">
                            {{ strtoupper($locale) }}
                            @if($locale === $defaultLocale)
                                <span class="ml-1 text-[10px] text-indigo-600 font-bold">(Default)</span>
                            @else
                                <button type="button" wire:click="removeLocale('{{ $locale }}')" class="ml-2 text-slate-500 hover:text-rose-500 font-bold">
                                    &times;
                                </button>
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Google AdSense Configurator -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl space-y-6">
            <h2 class="text-lg font-bold text-white border-b border-slate-800 pb-3">Google AdSense Integration</h2>
            
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">AdSense Publisher Client ID</label>
                <input wire:model="adsenseClientId" type="text" 
                    class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white placeholder-slate-655 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                    placeholder="ca-pub-XXXXXXXXXXXXXXXX">
                <p class="text-[10px] text-slate-500 mt-1">Leave empty to completely disable all banner displays across this site.</p>
                @error('adsenseClientId') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-450">Top Banner Ad Slot ID</label>
                    <input wire:model="adsenseTopSlot" type="text" 
                        class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="e.g. 1234567890">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-455">Sidebar Ad Slot ID</label>
                    <input wire:model="adsenseSidebarSlot" type="text" 
                        class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="e.g. 0987654321">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-450">In-Article Ad Slot ID</label>
                    <input wire:model="adsenseArticleSlot" type="text" 
                        class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="e.g. 1122334455">
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" 
                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-white font-semibold transition-colors shadow-lg shadow-indigo-600/10">
                Save Site Settings
            </button>
        </div>
    </form>
</div>
