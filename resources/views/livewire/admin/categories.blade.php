<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white">Categories</h1>
            <p class="text-slate-400 mt-1">Organize your blog posts into taxonomies.</p>
        </div>
        <button wire:click="toggleCreate" 
            class="px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors shadow-lg shadow-indigo-600/10">
            {{ $isCreating ? 'Back to List' : 'Create Category' }}
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @if($isCreating)
            <!-- Create/Edit Form (Centered / Spanned) -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl lg:col-span-2">
                <h2 class="text-xl font-bold text-white mb-6">
                    {{ $editingCategoryId ? 'Edit Category' : 'Create Category' }}
                </h2>

                <form wire:submit.prevent="saveCategory" class="space-y-6">
                    @foreach($supportedLocales as $locale)
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Category Name ({{ strtoupper($locale) }})
                            </label>
                            <input wire:model.live="names.{{ $locale }}" type="text" 
                                class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white placeholder-slate-650 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                                placeholder="Technology" required>
                            @error('names.' . $locale) <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    @endforeach

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Slug</label>
                        <input wire:model="slug" type="text" 
                            class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white placeholder-slate-650 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                            placeholder="technology" required>
                        @error('slug') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" wire:click="toggleCreate" 
                            class="px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-slate-400 hover:text-white transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-white font-semibold transition-colors">
                            Save Category
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- Categories Table (Spans full width when not editing) -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl lg:col-span-3">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase tracking-wider bg-slate-950/40">
                                <th class="px-6 py-4">Name (Translations)</th>
                                <th class="px-6 py-4">Slug</th>
                                <th class="px-6 py-4">Linked Posts</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            @forelse($categories as $category)
                                <tr class="hover:bg-slate-900/20 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-white">
                                            {{ $category->name[app()->getLocale()] ?? reset($category->name) }}
                                        </div>
                                        <div class="flex flex-wrap gap-1 mt-1 text-[10px]">
                                            @foreach($category->name as $lang => $translatedName)
                                                <span class="px-1.5 py-0.5 rounded bg-slate-950 text-slate-400 border border-slate-800 uppercase">
                                                    {{ $lang }}: {{ $translatedName }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-300">
                                        <code>{{ $category->slug }}</code>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-400">
                                        {{ $category->posts()->count() }}
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-3 text-sm font-medium">
                                        <button wire:click="editCategory({{ $category->id }})" 
                                            class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">
                                            Edit
                                        </button>
                                        <button onclick="confirm('Are you sure you want to delete this category? Associated posts will set their category mapping to none.') || event.stopImmediatePropagation()"
                                            wire:click="deleteCategory({{ $category->id }})" 
                                            class="text-rose-500 hover:text-rose-400 font-semibold transition-colors">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                        No categories defined yet. Click "Create Category" to build one.
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
