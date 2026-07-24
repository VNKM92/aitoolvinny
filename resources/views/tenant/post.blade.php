<x-tenant-layout :tenant="$tenant" :pages="$pages" :locale="$locale" :seo="$seo" :jsonLd="$jsonLd">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Post Body -->
        <article class="lg:col-span-3 backdrop-blur-xl bg-slate-900/20 border border-slate-900 rounded-2xl p-6 md:p-8 space-y-6">
            <!-- Meta details -->
            <div class="space-y-4">
                @if($post->category)
                    <a href="{{ route('tenant.category', ['slug' => $post->category->slug, 'locale' => $locale]) }}" 
                        class="text-xs font-bold uppercase tracking-widest text-pink-400 hover:underline">
                        {{ $post->category->name[$locale] ?? reset($post->category->name) }}
                    </a>
                @endif

                <h1 class="text-3xl md:text-5xl font-extrabold text-white leading-tight">
                    {{ $post->title[$locale] ?? reset($post->title) }}
                </h1>

                <div class="flex items-center space-x-3 text-xs text-slate-500 font-semibold">
                    <span>Published: {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
                    <span>&bull;</span>
                    <span>By: {{ $tenant->name }}</span>
                </div>
            </div>

            <!-- Featured Image -->
            @if($post->featured_image)
                <div class="h-[300px] md:h-[400px] w-full rounded-xl overflow-hidden border border-slate-900">
                    <img src="{{ asset('storage/' . $post->featured_image) }}" 
                         alt="{{ $post->title[$locale] ?? reset($post->title) }}" 
                         class="w-full h-full object-cover">
                </div>
            @endif

            <!-- In-Article Google AdSense (Rendered in-between details if enabled) -->
            @if($post->adsense_enabled && !empty($tenant->settings['adsense_client_id']) && !empty($tenant->settings['adsense_article_slot']))
                <div class="py-4 border-y border-slate-900/60 flex justify-center">
                    <!-- AdSense Article Placement -->
                    <ins class="adsbygoogle"
                         style="display:block; text-align:center;"
                         data-ad-layout="in-article"
                         data-ad-format="fluid"
                         data-ad-client="{{ $tenant->settings['adsense_client_id'] }}"
                         data-ad-slot="{{ $tenant->settings['adsense_article_slot'] }}"></ins>
                    <script>
                         (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
            @endif

            <!-- Text Content -->
            <div class="prose prose-invert prose-indigo max-w-none text-slate-350 leading-relaxed text-md space-y-4">
                {!! nl2br($post->content[$locale] ?? reset($post->content)) !!}
            </div>
        </article>

        <!-- Sidebar Widgets -->
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
        </aside>
    </div>
</x-tenant-layout>
