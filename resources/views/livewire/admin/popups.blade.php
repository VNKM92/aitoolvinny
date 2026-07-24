<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white">Popup Manager</h1>
            <p class="text-slate-400 mt-1">Manage localized overlay announcements and popup schedules.</p>
        </div>
        <button wire:click="toggleCreate" 
            class="px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors shadow-lg shadow-indigo-600/10">
            {{ $isCreating ? 'Back to List' : 'Create Popup' }}
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @if($isCreating)
            <!-- Create/Edit Form -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl lg:col-span-2">
                <h2 class="text-xl font-bold text-white mb-6">
                    {{ $editingPopupId ? 'Edit Popup Details' : 'Design Overlay Popup' }}
                </h2>

                <form wire:submit.prevent="savePopup" class="space-y-6">
                    <!-- Localized Inputs -->
                    @foreach($supportedLocales as $locale)
                        <div class="space-y-4 bg-slate-950/20 p-4 rounded-xl border border-slate-850">
                            <div class="text-xs font-bold text-slate-500 uppercase tracking-widest">Language: {{ strtoupper($locale) }}</div>
                            
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Popup Headline</label>
                                <input wire:model="titles.{{ $locale }}" type="text" 
                                    class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                                    placeholder="Flash Sale: 50% Off!" required>
                                @error('titles.' . $locale) <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Popup Content (HTML supported)</label>
                                <textarea wire:model="contents.{{ $locale }}" rows="4"
                                    class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                                    placeholder="Enter discount details and subscribe CTA button..." required></textarea>
                                @error('contents.' . $locale) <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endforeach

                    <!-- Global Configs -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-950/25 p-6 rounded-xl border border-slate-850">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Start Display Date</label>
                            <input wire:model="starts_at" type="datetime-local" 
                                class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @error('starts_at') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">End Display Date</label>
                            <input wire:model="ends_at" type="datetime-local" 
                                class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @error('ends_at') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2 flex items-center pt-2">
                            <label class="flex items-center cursor-pointer select-none">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div class="w-5 h-5 bg-slate-950 border border-slate-800 rounded flex items-center justify-center peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all duration-200 mr-2">
                                    <svg class="w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm text-slate-400 font-semibold">Enable and activate this popup campaign</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" wire:click="toggleCreate" 
                            class="px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-slate-400 hover:text-white transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-white font-semibold transition-colors">
                            Save Popup Campaign
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- Popups List Table -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl lg:col-span-3">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase tracking-wider bg-slate-950/40">
                                <th class="px-6 py-4">Headline (Translations)</th>
                                <th class="px-6 py-4">Schedule Dates</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            @forelse($popups as $popup)
                                <tr class="hover:bg-slate-900/20 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-white">
                                            {{ $popup->title[app()->getLocale()] ?? reset($popup->title) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-400">
                                        @if($popup->starts_at || $popup->ends_at)
                                            <div>Start: {{ $popup->starts_at ? $popup->starts_at->format('M d, Y') : 'Immediate' }}</div>
                                            <div class="mt-0.5">End: {{ $popup->ends_at ? $popup->ends_at->format('M d, Y') : 'Infinite' }}</div>
                                        @else
                                            <span class="text-slate-500">Always Displaying</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <button wire:click="togglePopupStatus({{ $popup->id }})" 
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border transition-all cursor-pointer {{ $popup->is_active ? 'bg-emerald-950/20 text-emerald-400 border-emerald-900/55' : 'bg-rose-950/20 text-rose-400 border-rose-900/55' }}">
                                            {{ $popup->is_active ? 'Active' : 'Disabled' }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-3 text-sm font-medium">
                                        <button wire:click="editPopup({{ $popup->id }})" 
                                            class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">
                                            Edit
                                        </button>
                                        <button onclick="confirm('Delete this popup?') || event.stopImmediatePropagation()"
                                            wire:click="deletePopup({{ $popup->id }})" 
                                            class="text-rose-500 hover:text-rose-455 font-semibold transition-colors">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                        No overlay announcements or popups designed yet. Click "Create Popup" to get started.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
