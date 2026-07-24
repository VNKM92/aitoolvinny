<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white">Posts</h1>
            <p class="text-slate-400 mt-1">Write articles and distribute contents on your website.</p>
        </div>
        <button wire:click="toggleCreate" 
            class="px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors shadow-lg shadow-indigo-600/10">
            {{ $isCreating ? 'Back to List' : 'Write Post' }}
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    @if($isCreating)
        <!-- Write/Edit Form -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-8 rounded-2xl shadow-2xl max-w-4xl mx-auto">
            <h2 class="text-xl font-bold text-white mb-6">
                {{ $editingPostId ? 'Edit Post Details' : 'Compose Blog Post' }}
            </h2>

            <form wire:submit.prevent="savePost" class="space-y-6">
                <!-- Localized Input Sections -->
                <div class="space-y-6 bg-slate-950/30 p-6 rounded-xl border border-slate-850">
                    <h3 class="text-md font-bold text-indigo-400 uppercase tracking-wide">Localized Content</h3>
                    
                    @foreach($supportedLocales as $locale)
                        <div class="space-y-4 border-b border-slate-800/60 pb-6 last:border-none last:pb-0">
                            <div class="text-xs font-extrabold text-slate-500 uppercase tracking-widest">Language: {{ strtoupper($locale) }}</div>
                            
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Post Title</label>
                                <input wire:model.live="titles.{{ $locale }}" type="text" 
                                    class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                                    placeholder="Enter title (e.g. My First Article)" required>
                                @error('titles.' . $locale) <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Content / Body</label>
                                <textarea wire:model="contents.{{ $locale }}" rows="6"
                                    class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                                    placeholder="Compose your article body here... HTML tags are supported." required></textarea>
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
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-450">SEO Description</label>
                                    <input wire:model="meta_descriptions.{{ $locale }}" type="text" 
                                        class="mt-1.5 block w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                                        placeholder="Meta tag description snippet">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Global Metadata & Settings -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-950/30 p-6 rounded-xl border border-slate-850">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Slug URL Path</label>
                        <input wire:model="slug" type="text" 
                            class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                            placeholder="my-first-article" required>
                        @error('slug') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Category</label>
                        <select wire:model="category_id" 
                            class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name[app()->getLocale()] ?? reset($category->name) }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
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

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Publish Schedule Date</label>
                        <input wire:model="published_at" type="datetime-local" 
                            class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        @error('published_at') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Featured Image</label>
                        <input type="file" wire:model="featured_image" class="mt-1.5 block text-slate-400 text-xs">
                        
                        <div class="mt-3 flex items-center space-x-4">
                            @if ($featured_image)
                                <div>
                                    <span class="text-[10px] text-slate-500 block mb-1">New Image Preview:</span>
                                    <img src="{{ $featured_image->temporaryUrl() }}" class="h-20 w-32 object-cover border border-slate-800 rounded-lg">
                                </div>
                            @elseif ($existing_featured_image)
                                <div>
                                    <span class="text-[10px] text-slate-500 block mb-1">Current Active Image:</span>
                                    <img src="{{ asset('storage/' . $existing_featured_image) }}" class="h-20 w-32 object-cover border border-slate-800 rounded-lg">
                                </div>
                            @endif
                        </div>
                        @error('featured_image') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-2 flex items-center pt-2">
                        <label class="flex items-center cursor-pointer select-none">
                            <input type="checkbox" wire:model="adsense_enabled" class="sr-only peer">
                            <div class="w-5 h-5 bg-slate-950 border border-slate-800 rounded flex items-center justify-center peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all duration-200 mr-2">
                                <svg class="w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm text-slate-400">Inject Google AdSense Units on this post page</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" wire:click="toggleCreate" 
                        class="px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-slate-400 hover:text-white transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-white font-semibold transition-colors">
                        Save and Publish
                    </button>
                </div>
            </form>
        </div>
    @else
        <!-- Posts List Table -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase tracking-wider bg-slate-950/40">
                            <th class="px-6 py-4">Article</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Publish Date</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($posts as $post)
                            <tr class="hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        @if($post->featured_image)
                                            <img src="{{ asset('storage/' . $post->featured_image) }}" class="h-10 w-16 object-cover border border-slate-850 rounded">
                                        @else
                                            <div class="h-10 w-16 bg-slate-950 border border-slate-850 rounded flex items-center justify-center text-[10px] text-slate-600 uppercase font-bold">No img</div>
                                        @endif
                                        <div>
                                            <div class="font-semibold text-white truncate max-w-xs">{{ $post->title[app()->getLocale()] ?? reset($post->title) }}</div>
                                            <div class="text-[10px] text-slate-500">Slug: /posts/{{ $post->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($post->category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-900 text-pink-400 border border-pink-950">
                                            {{ $post->category->name[app()->getLocale()] ?? reset($post->category->name) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-500">Uncategorized</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $post->status === 'published' ? 'bg-emerald-950/20 text-emerald-400 border-emerald-900/55' : 'bg-slate-900 text-slate-400 border-slate-800' }}">
                                        {{ ucfirst($post->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400">
                                    {{ $post->published_at ? $post->published_at->format('M d, Y @ H:i') : 'Immediate' }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-3 text-sm font-medium">
                                    <button wire:click="editPost({{ $post->id }})" 
                                        class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">
                                        Edit
                                    </button>
                                    <button onclick="confirm('Are you sure you want to delete this blog post? This action cannot be undone.') || event.stopImmediatePropagation()"
                                        wire:click="deletePost({{ $post->id }})" 
                                        class="text-rose-500 hover:text-rose-400 font-semibold transition-colors">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                    No blog posts created yet. Click "Write Post" to start publishing.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 bg-slate-950/20 border-t border-slate-800/40">
                {{ $posts->links() }}
            </div>
        </div>
    @endif
</div>
