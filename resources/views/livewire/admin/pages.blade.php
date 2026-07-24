<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white">Pages</h1>
            <p class="text-slate-400 mt-1">Manage static pages like About, Contact, or Terms.</p>
        </div>
        <button wire:click="toggleCreate" 
            class="px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors shadow-lg shadow-indigo-600/10">
            {{ $isCreating ? 'Back to List' : 'Create Page' }}
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    @if($isCreating)
        <!-- Create/Edit Form -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-8 rounded-2xl shadow-2xl max-w-4xl mx-auto">
            <h2 class="text-xl font-bold text-white mb-6">
                {{ $editingPageId ? 'Edit Page Details' : 'Create Static Page' }}
            </h2>

            <form wire:submit.prevent="savePage" class="space-y-6">
                <!-- Localized Input Sections -->
                <div class="space-y-6 bg-slate-950/30 p-6 rounded-xl border border-slate-850">
                    <h3 class="text-md font-bold text-indigo-400 uppercase tracking-wide">Localized Content</h3>
                    
                    @foreach($supportedLocales as $locale)
                        <div class="space-y-4 border-b border-slate-800/60 pb-6 last:border-none last:pb-0">
                            <div class="text-xs font-extrabold text-slate-500 uppercase tracking-widest">Language: {{ strtoupper($locale) }}</div>
                            
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Page Title</label>
                                <input wire:model.live="titles.{{ $locale }}" type="text" 
                                    class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                                    placeholder="Enter title (e.g. About Us)" required>
                                @error('titles.' . $locale) <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Content / Body</label>
                                <textarea wire:model="contents.{{ $locale }}" rows="6"
                                    class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                                    placeholder="Compose your page body here... HTML tags are supported." required></textarea>
                                @error('contents.' . $locale) <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-450">SEO Title Override</label>
                                    <input wire:model="meta_titles.{{ $locale }}" type="text" 
                                        class="mt-1.5 block w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                                        placeholder="SEO optimized title">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-455">SEO Description</label>
                                    <input wire:model="meta_descriptions.{{ $locale }}" type="text" 
                                        class="mt-1.5 block w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                                        placeholder="Meta tag description snippet">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Global Metadata & Settings -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-950/30 p-6 rounded-xl border border-slate-855">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Slug URL Path</label>
                        <input wire:model="slug" type="text" 
                            class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                            placeholder="about-us" required>
                        @error('slug') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Status</label>
                        <select wire:model="status" 
                            class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                        @error('status') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" wire:click="toggleCreate" 
                        class="px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-slate-400 hover:text-white transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-white font-semibold transition-colors">
                        Save Page
                    </button>
                </div>
            </form>
        </div>
    @else
        <!-- Pages List Table -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase tracking-wider bg-slate-950/40">
                            <th class="px-6 py-4">Page Title</th>
                            <th class="px-6 py-4">Slug</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Last Updated</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($pages as $page)
                            <tr class="hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-white">
                                        {{ $page->title[app()->getLocale()] ?? reset($page->title) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-300">
                                    <code>/pages/{{ $page->slug }}</code>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $page->status === 'published' ? 'bg-emerald-950/20 text-emerald-400 border-emerald-900/55' : 'bg-slate-900 text-slate-400 border-slate-800' }}">
                                        {{ ucfirst($page->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400">
                                    {{ $page->updated_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-3 text-sm font-medium">
                                    <button wire:click="editPage({{ $page->id }})" 
                                        class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">
                                        Edit
                                    </button>
                                    <button onclick="confirm('Are you sure you want to delete this static page?') || event.stopImmediatePropagation()"
                                        wire:click="deletePage({{ $page->id }})" 
                                        class="text-rose-500 hover:text-rose-400 font-semibold transition-colors">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                    No static pages created yet. Click "Create Page" to add one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
