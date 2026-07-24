<x-tenant-layout :tenant="$tenant" :pages="$pages" :locale="$locale" :seo="$seo" :jsonLd="$jsonLd">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Blog List -->
        <div class="lg:col-span-3 space-y-8">
            @if(isset($category))
                <div class="p-4 bg-slate-900 border border-slate-800 rounded-xl mb-6 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-500 uppercase tracking-wider">Filtered Category</span>
                        <h2 class="text-xl font-bold text-white mt-0.5">{{ $category->name[$locale] ?? reset($category->name) }}</h2>
                    </div>
                    <a href="{{ route('tenant.home', ['locale' => $locale]) }}" class="text-xs text-indigo-400 font-semibold hover:underline">Clear Filter</a>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @forelse($posts as $post)
                    <article class="backdrop-blur-xl bg-slate-900/40 border border-slate-900 rounded-2xl overflow-hidden hover:border-slate-800/80 transition-all group flex flex-col justify-between">
                        <div>
                            @if($post->featured_image)
                                <a href="{{ route('tenant.post', ['slug' => $post->slug, 'locale' => $locale]) }}" class="block overflow-hidden h-48">
                                    <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                         alt="{{ $post->title[$locale] ?? reset($post->title) }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                                </a>
                            @else
                                <div class="h-48 bg-slate-950 flex items-center justify-center border-b border-slate-900">
                                    <svg class="h-12 w-12 text-slate-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 00-2-2m2 2v10a2 2 0 01-2 2M9 9h6m-6 4h6m-6 4h3" />
                                    </svg>
                                </div>
                            @endif

                            <div class="p-5">
                                @if($post->category)
                                    <a href="{{ route('tenant.category', ['slug' => $post->category->slug, 'locale' => $locale]) }}" 
                                        class="inline-block text-[10px] font-bold uppercase tracking-wider text-pink-400 mb-2 hover:underline">
                                        {{ $post->category->name[$locale] ?? reset($post->category->name) }}
                                    </a>
                                @endif

                                <h3 class="text-lg font-bold text-white leading-snug group-hover:text-indigo-400 transition-colors">
                                    <a href="{{ route('tenant.post', ['slug' => $post->slug, 'locale' => $locale]) }}">
                                        {{ $post->title[$locale] ?? reset($post->title) }}
                                    </a>
                                </h3>

                                <p class="text-xs text-slate-400 mt-2 line-clamp-3">
                                    {{ substr(strip_tags($post->content[$locale] ?? reset($post->content)), 0, 150) }}...
                                </p>
                            </div>
                        </div>

                        <div class="px-5 py-4 border-t border-slate-900/60 flex items-center justify-between text-[10px] text-slate-500 font-semibold bg-slate-950/20">
                            <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
                            <a href="{{ route('tenant.post', ['slug' => $post->slug, 'locale' => $locale]) }}" class="text-indigo-400 hover:text-indigo-300">Read More &rarr;</a>
                        </div>
                    </article>
                @empty
                    <div class="sm:col-span-2 p-12 text-center text-slate-500 backdrop-blur-xl bg-slate-900/20 border border-slate-900 rounded-2xl">
                        No articles published yet. Stay tuned!
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="pt-6">
                {{ $posts->links() }}
            </div>
        </div>

        <!-- Sidebar (Categories & Ads) -->
        <aside class="space-y-6">
            <!-- Categories Widget -->
            <div class="backdrop-blur-xl bg-slate-900/40 border border-slate-900 p-6 rounded-2xl">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider border-b border-slate-800 pb-3 mb-4">Categories</h3>
                <nav class="space-y-2">
                    @forelse($categories as $cat)
                        <a href="{{ route('tenant.category', ['slug' => $cat->slug, 'locale' => $locale]) }}" 
                            class="flex items-center justify-between text-sm font-medium text-slate-400 hover:text-indigo-400 transition-colors py-1">
                            <span>{{ $cat->name[$locale] ?? reset($cat->name) }}</span>
                            <span class="text-xs px-2 py-0.5 bg-slate-950 text-slate-500 border border-slate-900 rounded-md">{{ $cat->posts()->count() }}</span>
                        </a>
                    @empty
                        <span class="text-xs text-slate-650">No categories mapped.</span>
                    @endforelse
                </nav>
            </div>

            <!-- Google AdSense Sidebar Widget -->
            @if(!empty($tenant->settings['adsense_client_id']) && !empty($tenant->settings['adsense_sidebar_slot']))
                <div class="backdrop-blur-xl bg-slate-900/40 border border-slate-900 p-4 rounded-2xl flex justify-center overflow-hidden">
                    <!-- AdSense Sidebar Unit -->
                    <ins class="adsbygoogle"
                         style="display:block"
                         data-ad-client="{{ $tenant->settings['adsense_client_id'] }}"
                         data-ad-slot="{{ $tenant->settings['adsense_sidebar_slot'] }}"
                         data-ad-format="auto"
                         data-full-width-responsive="true"></ins>
                    <script>
                         (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
            @endif
        </aside>
    </div>
</x-tenant-layout>
