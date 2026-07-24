<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white">Websites</h1>
            <p class="text-slate-400 mt-1">Manage tenant websites, domains, and administrator accounts.</p>
        </div>
        <button wire:click="toggleCreate" 
            class="px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors shadow-lg shadow-indigo-600/10">
            {{ $isCreating ? 'Back to List' : 'Create Website' }}
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    @if($isCreating)
        <!-- Create Website Form -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-8 rounded-2xl shadow-2xl max-w-2xl mx-auto">
            <h2 class="text-xl font-bold text-white mb-6">Setup New Tenant Website</h2>
            
            <form wire:submit.prevent="saveTenant" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Website Name</label>
                        <input wire:model="name" type="text" 
                            class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                            placeholder="My Tech Blog" required>
                        @error('name') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Subdomain</label>
                        <input wire:model="subdomain" type="text" 
                            class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                            placeholder="techblog" required>
                        @error('subdomain') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Default Locale</label>
                    <select wire:model="default_locale" 
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        <option value="en">English (en)</option>
                        <option value="es">Spanish (es)</option>
                        <option value="fr">French (fr)</option>
                    </select>
                    @error('default_locale') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="border-t border-slate-800 pt-6">
                    <h3 class="text-md font-bold text-white mb-4">Initial Admin Account</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Admin Name</label>
                            <input wire:model="admin_name" type="text" 
                                class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                                placeholder="John Doe" required>
                            @error('admin_name') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Admin Email</label>
                            <input wire:model="admin_email" type="email" 
                                class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                                placeholder="john@example.com" required>
                            @error('admin_email') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Admin Password</label>
                        <input wire:model="admin_password" type="password" 
                            class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                            placeholder="••••••••" required>
                        @error('admin_password') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="toggleCreate" 
                        class="px-5 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-slate-400 hover:text-white transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-white font-semibold transition-colors shadow-lg shadow-indigo-600/10">
                        Create Website
                    </button>
                </div>
            </form>
        </div>
    @elseif($isMappingDomain)
        <!-- Map Custom Domain Form -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-8 rounded-2xl shadow-2xl max-w-md mx-auto">
            <h2 class="text-xl font-bold text-white mb-6">Map Custom Domain</h2>
            
            <form wire:submit.prevent="mapDomain" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Hostname / Domain</label>
                    <input wire:model="new_domain" type="text" 
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="e.g. www.clientblog.com" required>
                    @error('new_domain') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" wire:click="toggleMapDomain" 
                        class="px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-slate-400 hover:text-white transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-white font-semibold transition-colors">
                        Map Domain
                    </button>
                </div>
            </form>
        </div>
    @else
        <!-- Websites Grid -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase tracking-wider bg-slate-950/40">
                            <th class="px-6 py-4">Website</th>
                            <th class="px-6 py-4">Subdomain</th>
                            <th class="px-6 py-4">Mapped Domains</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($tenants as $tenant)
                            <tr class="hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-white">{{ $tenant->name }}</div>
                                    <div class="text-xs text-slate-500">Default Locale: {{ $tenant->default_locale }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-300">
                                    <code>{{ $tenant->subdomain }}</code>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5 max-w-xs">
                                        @foreach($tenant->domains as $domain)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-900 text-indigo-400 border border-indigo-950">
                                                {{ $domain->domain }}
                                                @if($domain->is_primary)
                                                    <span class="ml-1 text-[10px] text-indigo-500 font-bold">(Primary)</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="toggleTenantStatus({{ $tenant->id }})" 
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border transition-all cursor-pointer {{ $tenant->is_active ? 'bg-emerald-950/20 text-emerald-400 border-emerald-900/55' : 'bg-rose-950/20 text-rose-400 border-rose-900/55' }}">
                                        {{ $tenant->is_active ? 'Active' : 'Suspended' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 text-sm font-medium">
                                    <button wire:click="toggleMapDomain({{ $tenant->id }})" 
                                        class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">
                                        Map Domain
                                    </button>
                                    <button onclick="confirm('Are you sure you want to delete this website? All files, pages, and blog posts will be permanently destroyed.') || event.stopImmediatePropagation()"
                                        wire:click="deleteTenant({{ $tenant->id }})" 
                                        class="text-rose-500 hover:text-rose-400 font-semibold transition-colors">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                    No websites registered yet. Click "Create Website" to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
