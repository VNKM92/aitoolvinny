<div class="space-y-6">
    <div class="flex items-center justify-between pb-6 border-b border-slate-900">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight flex items-center">
                <svg class="h-6 w-6 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Traffic SEO Tools Manager
            </h2>
            <p class="text-xs text-slate-400 mt-1">Configure taglines, toggle active status, and customize SEO titles/descriptions for all 20 online tools.</p>
        </div>
    </div>

    <!-- Message Flash Alert -->
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-xl text-xs font-semibold">
            {{ session('message') }}
        </div>
    @endif

    @if($isEditing)
        <div class="bg-slate-900/40 backdrop-blur-md border border-slate-900 p-6 rounded-2xl space-y-4">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                Edit Configuration: <span class="text-indigo-400 font-mono font-bold">{{ $toolSlug }}</span>
            </h3>
            <form wire:submit.prevent="saveTool" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Display Tool Name</label>
                        <input wire:model="toolNameEn" type="text" class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        @error('toolNameEn') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center pt-6">
                        <label class="flex items-center cursor-pointer select-none">
                            <input wire:model="toolIsActive" type="checkbox" class="sr-only peer">
                            <div class="w-5 h-5 bg-slate-950 border border-slate-850 rounded flex items-center justify-center peer-checked:bg-indigo-600 peer-checked:border-indigo-600 mr-2 transition-all">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-xs text-slate-400">Tool is active and accessible on public frontend</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Display Tagline Description</label>
                    <textarea wire:model="toolDescEn" rows="2" class="mt-1.5 block w-full p-3 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500" required></textarea>
                    @error('toolDescEn') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">SEO Meta Title Tag</label>
                        <input wire:model="toolMetaTitleEn" type="text" class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        @error('toolMetaTitleEn') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">SEO Meta Description Tag</label>
                        <input wire:model="toolMetaDescEn" type="text" class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        @error('toolMetaDescEn') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex space-x-3 pt-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-semibold text-white transition-colors">
                        Save Tool Settings
                    </button>
                    <button type="button" wire:click="cancelEdit" class="px-4 py-2 bg-slate-950 border border-slate-850 text-slate-400 hover:text-white rounded-xl text-xs font-semibold transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @else
        <!-- Tools grid table -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-850 text-slate-400 font-semibold uppercase tracking-wider bg-slate-950/40">
                            <th class="px-6 py-4">Tool Slug</th>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">SEO Meta Title</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850/50">
                        @foreach($tools as $tool)
                            <tr class="hover:bg-slate-900/10 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-indigo-400">
                                    {{ $tool->slug }}
                                </td>
                                <td class="px-6 py-4 text-white font-semibold">
                                    {{ $tool->translate('name', 'en') }}
                                </td>
                                <td class="px-6 py-4 text-slate-400 max-w-xs truncate" title="{{ $tool->translate('meta_title', 'en') }}">
                                    {{ $tool->translate('meta_title', 'en') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button wire:click="toggleToolStatus({{ $tool->id }})" 
                                        class="px-2 py-0.5 rounded text-[10px] font-bold uppercase transition-colors {{ $tool->is_active ? 'bg-emerald-950/20 text-emerald-400 border border-emerald-900/30' : 'bg-rose-950/20 text-rose-450 border border-rose-900/30' }}">
                                        {{ $tool->is_active ? 'Active' : 'Disabled' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="editTool({{ $tool->id }})" class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">Configure</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-slate-950/20 border-t border-slate-800/40">
                {{ $tools->links() }}
            </div>
        </div>
    @endif
</div>
