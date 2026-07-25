<x-tenant-layout :pages="$pages" :locale="$locale" :seo="$seo" :jsonLd="$jsonLd">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Blog Articles Feed -->
        <div class="lg:col-span-3 space-y-8">
            <div class="card glass-panel overflow-hidden p-8">
                <span class="text-xs font-semibold uppercase tracking-[0.4em] text-primary/90">Web Vitals-inspired design</span>
                <h1 class="mt-4 text-4xl sm:text-5xl font-semibold tracking-tight text-slate-900">Performance, clarity, and polish in every article.</h1>
                <p class="mt-4 max-w-3xl text-slate-600 text-lg leading-8">Browse our latest content and tools with a calm, modern interface built to feel fast, readable, and easy to use across devices.</p>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 mb-6">Latest Articles</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($posts as $index => $post)
                    <article class="card glass-panel overflow-hidden transition-transform duration-300 hover:-translate-y-1 hover:shadow-2xl flex flex-col justify-between group">
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
                                    <span class="text-[10px] font-bold uppercase tracking-[0.32em] text-primary">
                                        {{ $post->category->name[$locale] ?? reset($post->category->name) }}
                                    </span>
                                @endif
                                <h3 class="text-lg font-semibold text-slate-900 group-hover:text-primary transition-colors">
                                    <a href="{{ route('tenant.post', ['slug' => $post->slug, 'locale' => $locale]) }}">
                                        {{ $post->title[$locale] ?? reset($post->title) }}
                                    </a>
                                </h3>
                                <p class="text-sm text-slate-600 line-clamp-3 leading-relaxed">
                                    {{ strip_tags($post->content[$locale] ?? reset($post->content)) }}
                                </p>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-100 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                            <span>Published {{ $post->published_at ? $post->published_at->diffForHumans() : $post->created_at->diffForHumans() }}</span>
                            <a href="{{ route('tenant.post', ['slug' => $post->slug, 'locale' => $locale]) }}" class="font-semibold text-primary hover:text-primary-hover">Read More &rarr;</a>
                        </div>
                    </article>

                    <!-- In-Feed Ad slot every 2 posts -->
                    @if(($index + 1) % 2 === 0)
                        @php
                            $inFeedAd = \App\Services\AdRendererService::render('in_feed');
                        @endphp
                        @if(!empty(trim(strip_tags($inFeedAd, '<img><ins><iframe><a><script><div>'))))
                            <div class="md:col-span-2 backdrop-blur-md bg-slate-100/80 border border-slate-200 p-4 rounded-2xl flex justify-center items-center">
                                {!! $inFeedAd !!}
                            </div>
                        @endif
                    @endif
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
            <div class="card glass-panel border-white/10 p-6 rounded-2xl">
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-[0.3em] mb-3">Search Site</h4>
                <form action="{{ route('tenant.home', ['locale' => $locale]) }}" method="GET" class="flex flex-col sm:flex-row sm:items-center sm:space-x-2 gap-3">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="flex-1 bg-slate-100 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent px-4 py-3" 
                        placeholder="Search posts...">
                    <button type="submit" class="btn-secondary w-full sm:w-auto text-sm font-semibold">Search</button>
                </form>
            </div>

            <!-- Categories list -->
            <div class="card glass-panel border-white/10 p-6 rounded-2xl">
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-[0.3em] mb-3">Categories</h4>
                <ul class="space-y-3 text-sm font-medium text-slate-700">
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('tenant.category', ['slug' => $category->slug, 'locale' => $locale]) }}" class="flex items-center justify-between gap-3 hover:text-primary transition-colors">
                                <span>{{ $category->name[$locale] ?? reset($category->name) }}</span>
                                <span class="bg-slate-100 px-2 py-1 rounded-full text-[11px] text-slate-600">{{ $category->posts()->count() }}</span>
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
