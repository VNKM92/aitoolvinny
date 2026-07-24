<x-tenant-layout :pages="$pages" :locale="$locale" :seo="$seo" :jsonLd="$jsonLd">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Blog Articles Feed -->
        <div class="lg:col-span-3 space-y-8">
            <h2 class="text-2xl font-bold tracking-tight text-white mb-6">Latest Articles</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($posts as $post)
                    <article class="backdrop-blur-md bg-slate-900/40 border border-slate-900 rounded-2xl overflow-hidden hover:border-slate-800 transition-all flex flex-col justify-between group">
                        <div>
                            @if($post->featured_image)
                                <div class="h-48 overflow-hidden relative">
                                    <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                        alt="{{ $post->title[$locale] ?? reset($post->title) }}" 
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                            @endif
                            <div class="p-6 space-y-3">
                                @if($post->category)
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-pink-500">
                                        {{ $post->category->name[$locale] ?? reset($post->category->name) }}
                                    </span>
                                @endif
                                <h3 class="text-lg font-bold text-white group-hover:text-indigo-400 transition-colors">
                                    <a href="{{ route('tenant.post', ['slug' => $post->slug, 'locale' => $locale]) }}">
                                        {{ $post->title[$locale] ?? reset($post->title) }}
                                    </a>
                                </h3>
                                <p class="text-xs text-slate-400 line-clamp-3 leading-relaxed">
                                    {{ strip_tags($post->content[$locale] ?? reset($post->content)) }}
                                </p>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-950/40 border-t border-slate-900/50 flex items-center justify-between text-[10px] text-slate-500">
                            <span>Published {{ $post->published_at ? $post->published_at->diffForHumans() : $post->created_at->diffForHumans() }}</span>
                            <a href="{{ route('tenant.post', ['slug' => $post->slug, 'locale' => $locale]) }}" class="font-bold text-indigo-400 hover:text-indigo-300">Read More &rarr;</a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-2 py-12 text-center text-slate-500">
                        No articles published yet. Stay tuned!
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        </div>

        <!-- Sidebar (Taxonomies, Search, Newsletters) -->
        <div class="space-y-6">
            <!-- Search Widget -->
            <div class="backdrop-blur-md bg-slate-900/40 border border-slate-900 p-6 rounded-2xl">
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Search Site</h4>
                <form action="{{ route('tenant.home', ['locale' => $locale]) }}" method="GET" class="flex space-x-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="flex-1 px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                        placeholder="Search posts...">
                    <button type="submit" class="px-3 py-2 bg-slate-850 hover:bg-slate-800 border border-slate-800 rounded-lg text-xs text-slate-300">Go</button>
                </form>
            </div>

            <!-- Categories list -->
            <div class="backdrop-blur-md bg-slate-900/40 border border-slate-900 p-6 rounded-2xl">
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Categories</h4>
                <ul class="space-y-2 text-xs font-semibold text-slate-400">
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('tenant.category', ['slug' => $category->slug, 'locale' => $locale]) }}" class="hover:text-indigo-400 flex justify-between items-center transition-colors">
                                <span>{{ $category->name[$locale] ?? reset($category->name) }}</span>
                                <span class="bg-slate-950 px-2 py-0.5 rounded text-[10px] text-slate-650">{{ $category->posts()->count() }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Dynamic Newsletter Signup Widget -->
            <livewire:public.newsletter-form />
        </div>
    </div>
</x-tenant-layout>
