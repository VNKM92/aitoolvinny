<x-tenant-layout :pages="$pages" :locale="$locale" :seo="$seo" :jsonLd="$jsonLd">
    <div class="news-divider mb-6"></div>

    {{-- PAGE HEADER (Category / Subcategory filter labels, or home headline) --}}
    @if(isset($category))
        <header class="mb-8 py-4 px-2" style="border-left: 5px solid var(--theme-primary); background: var(--theme-section-bg);">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="eyebrow uppercase tracking-widest font-bold text-xs" style="color: var(--theme-primary);">Category</span>
                <h1 class="font-heading font-bold text-3xl md:text-5xl leading-tight" style="color: var(--theme-body-text);">
                    {{ $category->translate('name', $locale) }}
                </h1>
            </div>
            @if(!empty($category->subcategories) && $category->subcategories->count())
                <nav class="mt-4 flex flex-wrap gap-2">
                    <span class="text-xs uppercase tracking-wider" style="color: var(--theme-muted);">Filter:</span>
                    @foreach($category->subcategories as $sub)
                        <a href="{{ route('tenant.subcategory', ['slug' => $sub->slug, 'locale' => $locale]) }}"
                           class="text-xs font-semibold px-3 py-1 rounded-none border transition-colors"
                           style="border-color: var(--theme-border-color); color: var(--theme-body-text);">
                            {{ $sub->translate('name',$locale) }}
                        </a>
                    @endforeach
                </nav>
            @endif
        </header>
    @elseif(isset($subcategory))
        <header class="mb-8 py-4 px-2" style="border-left: 5px solid var(--theme-accent); background: var(--theme-section-bg);">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="eyebrow uppercase tracking-widest font-bold text-xs" style="color: var(--theme-accent);">Subcategory</span>
                @if($subcategory->category)
                    <a href="{{ route('tenant.category', ['slug' => $subcategory->category->slug, 'locale' => $locale]) }}"
                       class="text-xs font-semibold px-2 py-1 border"
                       style="border-color: var(--theme-border-color); color: var(--theme-body-text);">
                        {{ $subcategory->category->translate('name', $locale) }}
                    </a>
                @endif
                <h1 class="font-heading font-bold text-3xl md:text-5xl leading-tight" style="color: var(--theme-body-text);">
                    {{ $subcategory->translate('name',$locale) }}
                </h1>
            </div>
        </header>
    @else
        {{-- HOMEPAGE: FEATURED HERO GRID (1 big + 4 small) --}}
        @if($featuredPosts && $featuredPosts->count())
            <section class="mb-10">
                <div class="flex items-end justify-between mb-4">
                    <h2 class="font-heading font-black uppercase text-2xl md:text-3xl tracking-tighter" style="color: var(--theme-body-text);">
                        <span style="color: var(--theme-primary);">■</span> Featured Stories
                    </h2>
                    <a href="{{ route('tenant.home', ['locale' => $locale]) }}#latest" class="link-underline text-sm font-semibold" style="color: var(--theme-primary);">All Stories &rarr;</a>
                </div>
                <div class="news-divider mb-5"></div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    @php
                        $heroPost = $featuredPosts->first();
                        $sidePosts = $featuredPosts->skip(1)->take(4);
                    @endphp
                    @if($heroPost)
                        <article class="lg:col-span-2 news-card flex flex-col overflow-hidden group cursor-pointer" onclick="window.location='{{ route('tenant.post', ['slug' => $heroPost->slug, 'locale' => $locale]) }}'">
                            @if($heroPost->featured_image)
                                <div class="aspect-[16/9] overflow-hidden bg-slate-100">
                                    <img src="{{ $heroPost->featured_image }}"
                                         alt="{{ $heroPost->translate('title',$locale) }}"
                                         class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-700" loading="eager">
                                </div>
                            @endif
                            <div class="p-5 flex-1" style="background: var(--theme-card-bg); border: 1px solid var(--theme-border-color); border-top: 0;">
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    @if($heroPost->category)
                                        <span class="eyebrow font-bold">{{ $heroPost->category->translate('name',$locale) }}</span>
                                    @endif
                                    @if($heroPost->subcategory)
                                        <span class="text-[10px] uppercase tracking-widest font-semibold" style="color: var(--theme-accent);">
                                            / {{ $heroPost->subcategory->translate('name',$locale) }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="font-heading font-black text-2xl md:text-4xl leading-[1.08] group-hover:opacity-80 transition-opacity" style="color: var(--theme-body-text);">
                                    <a href="{{ route('tenant.post', ['slug' => $heroPost->slug, 'locale' => $locale]) }}">
                                        {{ $heroPost->translate('title',$locale) }}
                                    </a>
                                </h3>
                                <p class="mt-3 text-base leading-relaxed" style="color: var(--theme-muted);">
                                    {{ $heroPost->excerptText() }}
                                </p>
                                <div class="mt-5 flex items-center justify-between text-xs uppercase tracking-widest" style="color: var(--theme-muted);">
                                    <span>{{ $heroPost->published_at ? $heroPost->published_at->format('M d, Y') : $heroPost->created_at->format('M d, Y') }}</span>
                                    <span class="font-semibold" style="color: var(--theme-primary);">Read Story &rarr;</span>
                                </div>
                            </div>
                        </article>
                    @endif
                    <div class="grid grid-cols-2 lg:grid-cols-1 gap-5">
                        @foreach($sidePosts as $sp)
                            <article class="news-card overflow-hidden group" onclick="window.location='{{ route('tenant.post', ['slug' => $sp->slug, 'locale' => $locale]) }}'">
                                @if($sp->featured_image)
                                    <div class="aspect-[16/9] overflow-hidden">
                                        <img src="{{ $sp->featured_image }}"
                                             alt="{{ $sp->translate('title',$locale) }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    </div>
                                @endif
                                <div class="p-4" style="background: var(--theme-card-bg); border: 1px solid var(--theme-border-color); border-top:0;">
                                    @if($sp->category)
                                        <span class="eyebrow font-bold text-[10px]">
                                            {{ $sp->category->translate('name',$locale) }}
                                        </span>
                                    @endif
                                    <h4 class="font-heading font-bold leading-tight mt-2 group-hover:opacity-80 transition-opacity" style="color: var(--theme-body-text);">
                                        <a href="{{ route('tenant.post', ['slug' => $sp->slug, 'locale' => $locale]) }}">
                                            {{ $sp->translate('title',$locale) }}
                                        </a>
                                    </h4>
                                    <div class="mt-2 text-[10px] uppercase tracking-widest" style="color: var(--theme-muted);">
                                        {{ $sp->published_at ? $sp->published_at->diffForHumans() : $sp->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @endif

    {{-- MAIN GRID: CONTENT + SIDEBAR --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-8" id="latest">
        {{-- MAIN CONTENT: Latest posts grid --}}
        <div class="lg:col-span-3">
            <div class="flex items-end justify-between mb-4">
                <h2 class="font-heading font-black uppercase text-2xl md:text-3xl tracking-tighter" style="color: var(--theme-body-text);">
                    <span style="color: var(--theme-primary);">■</span>
                    @if(isset($category)) Stories in: {{ $category->translate('name', $locale) }}
                    @elseif(isset($subcategory)) Stories in: {{ $subcategory->translate('name',$locale) }}
                    @else Latest Stories
                    @endif
                </h2>
            </div>
            <div class="news-divider mb-6"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @forelse($posts as $idx => $post)
                    <article class="news-card group flex flex-col overflow-hidden">
                        @if($post->featured_image)
                            <a href="{{ route('tenant.post', ['slug' => $post->slug, 'locale' => $locale]) }}" class="aspect-[16/9] overflow-hidden block">
                                <img src="{{ $post->featured_image }}"
                                     alt="{{ $post->translate('title',$locale) }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            </a>
                        @endif
                        <div class="p-5 flex-1 flex flex-col" style="background: var(--theme-card-bg); border:1px solid var(--theme-border-color); border-top:0;">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                @if($post->category)
                                    <a href="{{ route('tenant.category', ['slug' => $post->category->slug, 'locale' => $locale]) }}" class="eyebrow font-bold">
                                        {{ $post->category->translate('name',$locale) }}
                                    </a>
                                @endif
                                @if($post->subcategory)
                                    <span class="text-[10px] uppercase tracking-widest font-semibold" style="color: var(--theme-accent);">
                                        / <a href="{{ route('tenant.subcategory', ['slug' => $post->subcategory->slug, 'locale' => $locale]) }}">
                                            {{ $post->subcategory->translate('name',$locale) }}
                                        </a>
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-heading font-bold text-xl leading-snug group-hover:opacity-80 transition-opacity" style="color: var(--theme-body-text);">
                                <a href="{{ route('tenant.post', ['slug' => $post->slug, 'locale' => $locale]) }}">
                                    {{ $post->translate('title',$locale) }}
                                </a>
                            </h3>
                            <p class="mt-2 text-sm leading-relaxed flex-1" style="color: var(--theme-muted);">
                                {{ $post->excerptText() }}
                            </p>
                            <div class="mt-4 pt-3 flex items-center justify-between text-[11px] uppercase tracking-widest"
                                 style="border-top: 1px solid var(--theme-border-color); color: var(--theme-muted);">
                                <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
                                <a href="{{ route('tenant.post', ['slug' => $post->slug, 'locale' => $locale]) }}" class="font-semibold" style="color: var(--theme-primary);">
                                    Read &rarr;
                                </a>
                            </div>
                        </div>
                    </article>

                    @if(($idx + 1) % 4 === 0)
                        @php($ad = \App\Services\AdRendererService::render('in_feed'))
                        @if(trim(strip_tags($ad)))
                            <div class="md:col-span-2 p-5 text-center" style="background: var(--theme-section-bg); border:1px dashed var(--theme-border-color);">
                                <span class="text-[10px] uppercase tracking-widest font-semibold" style="color: var(--theme-muted);">Advertisement</span>
                                <div class="mt-2">{!! $ad !!}</div>
                            </div>
                        @endif
                    @endif
                @empty
                    <div class="col-span-2 py-16 text-center" style="color: var(--theme-muted);">
                        <div class="font-heading font-bold text-2xl mb-2" style="color: var(--theme-body-text);">No Stories Published Yet</div>
                        <p>Check back soon for the latest updates.</p>
                    </div>
                @endforelse
            </div>

            @if($posts->hasPages())
                <div class="mt-8 pt-4" style="border-top: 3px double var(--theme-border-color);">
                    {{ $posts->onEachSide(1)->links() }}
                </div>
            @endif

            {{-- HOMEPAGE ONLY: GROUPED BY CATEGORY SECTIONS --}}
            @if(!isset($category) && !isset($subcategory) && !empty($groupedByCategory) && count($groupedByCategory))
                @foreach($groupedByCategory as $catSlug => $catBlock)
                    @if(empty($catBlock['posts'] ?? []) || !$catBlock['posts']->count()) @continue @endif
                    <section class="mt-14">
                        <div class="flex items-end justify-between mb-4">
                            <h2 class="font-heading font-black uppercase text-2xl md:text-3xl tracking-tighter" style="color: var(--theme-body-text);">
                                <span style="color: var(--theme-primary);">■</span>
                                {{ $catBlock['category']?->name[$locale] ?? $catBlock['label'] ?? $catSlug }}
                            </h2>
                            <a href="{{ route('tenant.category', ['slug' => $catBlock['category']?->slug ?? $catSlug, 'locale' => $locale]) }}"
                               class="link-underline text-sm font-semibold" style="color: var(--theme-primary);">
                                More in {{ $catBlock['category']?->name[$locale] ?? $catSlug }} &rarr;
                            </a>
                        </div>
                        <div class="news-divider mb-5"></div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                            @foreach($catBlock['posts']->take(4) as $cp)
                                <article class="news-card group overflow-hidden flex flex-col h-full">
                                    @if($cp->featured_image)
                                        <a href="{{ route('tenant.post', ['slug' => $cp->slug, 'locale' => $locale]) }}" class="aspect-[4/3] overflow-hidden block">
                                            <img src="{{ $cp->featured_image }}"
                                                 alt="{{ $cp->translate('title',$locale) }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                        </a>
                                    @endif
                                    <div class="p-4 flex-1 flex flex-col" style="background: var(--theme-card-bg); border:1px solid var(--theme-border-color); border-top:0;">
                                        @if($cp->subcategory)
                                            <a href="{{ route('tenant.subcategory', ['slug' => $cp->subcategory->slug, 'locale' => $locale]) }}"
                                               class="text-[10px] uppercase tracking-widest font-semibold mb-1" style="color: var(--theme-accent);">
                                                {{ $cp->subcategory->translate('name',$locale) }}
                                            </a>
                                        @endif
                                        <h4 class="font-heading font-bold leading-snug group-hover:opacity-80 transition-opacity" style="color: var(--theme-body-text);">
                                            <a href="{{ route('tenant.post', ['slug' => $cp->slug, 'locale' => $locale]) }}">
                                                {{ $cp->translate('title',$locale) }}
                                            </a>
                                        </h4>
                                        <div class="mt-auto pt-3 text-[10px] uppercase tracking-widest"
                                             style="border-top: 1px solid var(--theme-border-color); color: var(--theme-muted);">
                                            {{ $cp->published_at ? $cp->published_at->format('M d, Y') : $cp->created_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            @endif
        </div>

        {{-- RIGHT SIDEBAR --}}
        <aside class="space-y-6">
            {{-- SEARCH --}}
            <div class="p-5" style="background: var(--theme-card-bg); border:1px solid var(--theme-border-color);">
                <h4 class="font-heading font-black uppercase tracking-wider text-sm mb-4 pb-3"
                    style="color: var(--theme-body-text); border-bottom: 2px solid var(--theme-primary);">
                    Search
                </h4>
                <form action="{{ route('tenant.home', ['locale' => $locale]) }}" method="GET">
                    <div class="flex">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search posts..."
                               class="flex-1 px-3 py-2 text-sm"
                               style="background: var(--theme-section-bg); border:1px solid var(--theme-border-color); color: var(--theme-body-text);">
                        <button type="submit" class="px-4 py-2 text-sm font-bold uppercase tracking-wider text-white"
                                style="background: var(--theme-primary);">Go</button>
                    </div>
                </form>
            </div>

            {{-- TRENDING / LATEST MINI LIST --}}
            <div class="p-5" style="background: var(--theme-card-bg); border:1px solid var(--theme-border-color);">
                <h4 class="font-heading font-black uppercase tracking-wider text-sm mb-4 pb-3"
                    style="color: var(--theme-body-text); border-bottom: 2px solid var(--theme-primary);">
                    <span style="color: var(--theme-primary);">■</span> Trending Now
                </h4>
                <ul class="space-y-4">
                    @foreach(($latestPosts ?? collect())->take(6) as $rank => $lp)
                        <li class="flex gap-3 group">
                            <span class="font-heading font-black text-3xl leading-none shrink-0" style="color: var(--theme-border-color);">
                                {{ str_pad($rank + 1, 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex gap-1 mb-1">
                                    @if($lp->category)
                                        <a href="{{ route('tenant.category', ['slug' => $lp->category->slug, 'locale' => $locale]) }}"
                                           class="text-[9px] uppercase tracking-widest font-bold" style="color: var(--theme-primary);">
                                            {{ $lp->category->translate('name',$locale) }}
                                        </a>
                                    @endif
                                </div>
                                <h5 class="font-semibold text-sm leading-snug group-hover:opacity-80 transition-opacity" style="color: var(--theme-body-text);">
                                    <a href="{{ route('tenant.post', ['slug' => $lp->slug, 'locale' => $locale]) }}">
                                        {{ $lp->translate('title',$locale) }}
                                    </a>
                                </h5>
                                <span class="text-[10px] uppercase tracking-widest mt-1 block" style="color: var(--theme-muted);">
                                    {{ $lp->published_at ? $lp->published_at->diffForHumans() : $lp->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- CATEGORIES + SUBCATEGORIES TREE --}}
            <div class="p-5" style="background: var(--theme-card-bg); border:1px solid var(--theme-border-color);">
                <h4 class="font-heading font-black uppercase tracking-wider text-sm mb-4 pb-3"
                    style="color: var(--theme-body-text); border-bottom: 2px solid var(--theme-primary);">
                    Newspaper Sections
                </h4>
                <ul class="space-y-2">
                    @foreach(($categories ?? collect()) as $cat)
                        <li>
                            <a href="{{ route('tenant.category', ['slug' => $cat->slug, 'locale' => $locale]) }}"
                               class="font-heading font-bold text-sm flex items-center justify-between py-2 link-underline"
                               style="color: var(--theme-body-text);">
                                <span>{{ $cat->translate('name',$locale) }}</span>
                                <span class="text-[10px] font-bold px-2"
                                      style="background: var(--theme-section-bg); color: var(--theme-muted);">
                                    {{ $cat->posts()->count() }}
                                </span>
                            </a>
                            @if(!empty($cat->subcategories) && $cat->subcategories->count())
                                <ul class="pl-3 mt-1 space-y-1">
                                    @foreach($cat->subcategories as $sub)
                                        <li>
                                            <a href="{{ route('tenant.subcategory', ['slug' => $sub->slug, 'locale' => $locale]) }}"
                                               class="text-xs font-medium flex items-center justify-between py-1"
                                               style="color: var(--theme-muted);">
                                                <span>&mdash; {{ $sub->translate('name',$locale) }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- NEWSLETTER WIDGET --}}
            <div class="p-5 text-center" style="background: var(--theme-primary); color: white;">
                <div class="eyebrow uppercase tracking-widest text-[10px] font-bold opacity-80">Subscribe</div>
                <h4 class="font-heading font-black text-xl mt-2 mb-1">Daily Newsletter</h4>
                <p class="text-xs opacity-90 mb-4 leading-relaxed">
                    Get the biggest stories delivered to your inbox every morning.
                </p>
                <form action="{{ route('tenant.newsletter.subscribe') }}" method="POST" class="space-y-2 text-left">
                    @csrf
                    <input type="email" name="email" required placeholder="your@email.com"
                           class="w-full px-3 py-2 text-sm text-slate-900"
                           style="background: white; border: none; color: #0f172a;">
                    <button type="submit"
                            class="w-full py-2 text-xs font-black uppercase tracking-widest"
                            style="background: #0f172a; color: white;">
                        Subscribe Free
                    </button>
                </form>
            </div>

            {{-- SIDEBAR AD SLOT --}}
            @php($sideAd = \App\Services\AdRendererService::render('sidebar'))
            @if(trim(strip_tags($sideAd)))
                <div class="p-4 text-center" style="background: var(--theme-section-bg); border:1px dashed var(--theme-border-color);">
                    <span class="text-[10px] uppercase tracking-widest font-semibold" style="color: var(--theme-muted);">Sponsored</span>
                    <div class="mt-2">{!! $sideAd !!}</div>
                </div>
            @endif
        </aside>
    </div>
</x-tenant-layout>
