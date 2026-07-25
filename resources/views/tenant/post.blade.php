<x-tenant-layout :pages="$pages" :locale="$locale" :seo="$seo" :jsonLd="$jsonLd" :post="$post">
    <div class="news-divider mb-6"></div>

    {{-- BREADCRUMBS --}}
    <nav aria-label="Breadcrumb" class="mb-6 text-xs uppercase tracking-widest" style="color: var(--theme-muted);">
        <ol class="flex flex-wrap gap-2 items-center">
            <li><a href="{{ route('tenant.home', ['locale' => $locale]) }}" class="link-underline font-semibold" style="color: var(--theme-primary);">Home</a></li>
            <li>/</li>
            @if($post->category)
                <li>
                    <a href="{{ route('tenant.category', ['slug' => $post->category->slug, 'locale' => $locale]) }}"
                       class="link-underline font-semibold" style="color: var(--theme-primary);">
                        {{ $post->category->translate('name', $locale) }}
                    </a>
                </li>
                @if($post->subcategory)
                    <li>/</li>
                    <li>
                        <a href="{{ route('tenant.subcategory', ['slug' => $post->subcategory->slug, 'locale' => $locale]) }}"
                           class="link-underline font-semibold" style="color: var(--theme-accent);">
                            {{ $post->subcategory->translate('name', $locale) }}
                        </a>
                    </li>
                @endif
                <li>/</li>
            @endif
            <li class="truncate max-w-[260px]" style="color: var(--theme-body-text);">
                {{ $post->translate('title', $locale) }}
            </li>
        </ol>
    </nav>

    {{-- ARTICLE HEADER --}}
    <article class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <div class="lg:col-span-3">
            <header class="mb-8 pb-6" style="border-bottom: 3px double var(--theme-border-color);">
                <div class="flex flex-wrap gap-3 items-center mb-5">
                    @if($post->category)
                        <span class="eyebrow font-bold">{{ $post->category->translate('name', $locale) }}</span>
                    @endif
                    @if($post->subcategory)
                        <span class="text-[10px] uppercase tracking-widest font-semibold" style="color: var(--theme-accent);">
                            / {{ $post->subcategory->translate('name', $locale) }}
                        </span>
                    @endif
                </div>
                <h1 class="font-heading font-black leading-[1.02] tracking-tighter text-3xl md:text-5xl lg:text-[56px]" style="color: var(--theme-body-text);">
                    {{ $post->translate('title', $locale) }}
                </h1>
                @if($post->excerpt)
                    <p class="mt-5 text-lg md:text-xl leading-relaxed font-serif italic" style="color: var(--theme-muted);">
                        {{ $post->excerptText() }}
                    </p>
                @endif

                <div class="mt-7 flex flex-wrap gap-5 items-center justify-between text-xs uppercase tracking-widest" style="color: var(--theme-muted);">
                    <div class="flex flex-wrap gap-5 items-center">
                        <span class="font-semibold">
                            <time datetime="{{ $post->published_at ?? $post->created_at }}">
                                {{ $post->published_at ? $post->published_at->format('l, F jS, Y') : $post->created_at->format('l, F jS, Y') }}
                            </time>
                        </span>
                        <span>&middot;</span>
                        <span>{{ str_word_count(strip_tags($post->translate('content', $locale))) }} words</span>
                        <span>&middot;</span>
                        <span>
                            {{ max(1, (int) ceil(str_word_count(strip_tags($post->translate('content', $locale))) / 220)) }} min read
                        </span>
                    </div>
                    {{-- SIMPLE SOCIAL SHARE --}}
                    <div class="flex gap-2 items-center">
                        <span class="font-bold">Share:</span>
                        <a rel="noopener nofollow" target="_blank"
                           href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('tenant.post', ['slug' => $post->slug, 'locale' => $locale])) }}"
                           class="px-3 py-1 text-xs font-bold" style="background: #1877f2; color: white;">Fb</a>
                        <a rel="noopener nofollow" target="_blank"
                           href="https://twitter.com/intent/tweet?url={{ urlencode(route('tenant.post', ['slug' => $post->slug, 'locale' => $locale])) }}&text={{ urlencode($post->translate('title', $locale)) }}"
                           class="px-3 py-1 text-xs font-bold" style="background: #000; color: white;">X</a>
                        <a rel="noopener nofollow" target="_blank"
                           href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('tenant.post', ['slug' => $post->slug, 'locale' => $locale])) }}"
                           class="px-3 py-1 text-xs font-bold" style="background: #0a66c2; color: white;">In</a>
                        <a rel="noopener nofollow"
                           href="whatsapp://send?text={{ urlencode(($post->translate('title', $locale)) . ' ' . route('tenant.post', ['slug' => $post->slug, 'locale' => $locale])) }}"
                           class="px-3 py-1 text-xs font-bold" style="background: #25d366; color: white;">Wa</a>
                    </div>
                </div>
            </header>

            @if($post->featured_image)
                <figure class="mb-8 news-card overflow-hidden">
                    <img src="{{ $post->featured_image }}"
                         alt="{{ $post->translate('title', $locale) }}"
                         class="w-full h-auto object-cover" loading="eager">
                    <figcaption class="px-4 py-2 text-xs italic tracking-wider text-right"
                                style="background: var(--theme-section-bg); color: var(--theme-muted);">
                        Featured image
                    </figcaption>
                </figure>
            @endif

            @if($post->adsense_enabled)
                @php($topAd = \App\Services\AdRendererService::render('above_content'))
                @if(trim(strip_tags($topAd)))
                    <div class="mb-8 p-4 text-center" style="background: var(--theme-section-bg); border:1px dashed var(--theme-border-color);">
                        <span class="text-[10px] uppercase tracking-widest font-semibold" style="color: var(--theme-muted);">Advertisement</span>
                        <div class="mt-2">{!! $topAd !!}</div>
                    </div>
                @endif
            @endif

            {{-- ARTICLE BODY --}}
            <div class="prose-news max-w-none" id="article-body" style="color: var(--theme-body-text);">
                {!! $post->translate('content', $locale) !!}
            </div>

            {{-- TAGS --}}
            @if($post->tags && $post->tags->count())
                <div class="mt-10 pt-6" style="border-top: 3px double var(--theme-border-color);">
                    <h6 class="font-heading font-black uppercase tracking-wider text-xs mb-3" style="color: var(--theme-primary);">Tags</h6>
                    <div class="flex flex-wrap gap-2">
                        @foreach($post->tags as $tag)
                            <span class="px-3 py-1 text-xs font-semibold border"
                                  style="border-color: var(--theme-border-color); color: var(--theme-muted);">
                                #{{ $tag->translate('name', $locale) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($post->adsense_enabled)
                @php($btmAd = \App\Services\AdRendererService::render('below_content'))
                @if(trim(strip_tags($btmAd)))
                    <div class="mt-10 p-4 text-center" style="background: var(--theme-section-bg); border:1px dashed var(--theme-border-color);">
                        <span class="text-[10px] uppercase tracking-widest font-semibold" style="color: var(--theme-muted);">Advertisement</span>
                        <div class="mt-2">{!! $btmAd !!}</div>
                    </div>
                @endif
            @endif

            {{-- RELATED POSTS --}}
            @if(!empty($relatedPosts) && $relatedPosts->count())
                <section class="mt-14">
                    <div class="flex items-end justify-between mb-4">
                        <h2 class="font-heading font-black uppercase text-2xl md:text-3xl tracking-tighter" style="color: var(--theme-body-text);">
                            <span style="color: var(--theme-primary);">■</span> Related Stories
                        </h2>
                        <a href="{{ route('tenant.home', ['locale' => $locale]) }}" class="link-underline text-sm font-semibold" style="color: var(--theme-primary);">
                            All News &rarr;
                        </a>
                    </div>
                    <div class="news-divider mb-5"></div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        @foreach($relatedPosts as $rp)
                            <article class="news-card group flex flex-col overflow-hidden h-full">
                                @if($rp->featured_image)
                                    <a href="{{ route('tenant.post', ['slug' => $rp->slug, 'locale' => $locale]) }}" class="aspect-[16/9] overflow-hidden block">
                                        <img src="{{ $rp->featured_image }}"
                                             alt="{{ $rp->translate('title', $locale) }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    </a>
                                @endif
                                <div class="p-4 flex-1 flex flex-col" style="background: var(--theme-card-bg); border:1px solid var(--theme-border-color); border-top:0;">
                                    <div class="flex gap-1 mb-2 flex-wrap">
                                        @if($rp->category)
                                            <a href="{{ route('tenant.category', ['slug' => $rp->category->slug, 'locale' => $locale]) }}"
                                               class="eyebrow font-bold text-[10px]">
                                                {{ $rp->category->translate('name', $locale) }}
                                            </a>
                                        @endif
                                        @if($rp->subcategory)
                                            <a href="{{ route('tenant.subcategory', ['slug' => $rp->subcategory->slug, 'locale' => $locale]) }}"
                                               class="text-[10px] uppercase tracking-widest font-semibold" style="color: var(--theme-accent);">
                                                / {{ $rp->subcategory->translate('name', $locale) }}
                                            </a>
                                        @endif
                                    </div>
                                    <h4 class="font-heading font-bold leading-snug group-hover:opacity-80 transition-opacity" style="color: var(--theme-body-text);">
                                        <a href="{{ route('tenant.post', ['slug' => $rp->slug, 'locale' => $locale]) }}">
                                            {{ $rp->translate('title', $locale) }}
                                        </a>
                                    </h4>
                                    <div class="mt-auto pt-3 text-[10px] uppercase tracking-widest"
                                         style="border-top: 1px solid var(--theme-border-color); color: var(--theme-muted);">
                                        {{ $rp->published_at ? $rp->published_at->format('M d, Y') : $rp->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        {{-- SINGLE POST SIDEBAR --}}
        <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">
            <div class="p-5" style="background: var(--theme-card-bg); border:1px solid var(--theme-border-color);">
                <h4 class="font-heading font-black uppercase tracking-wider text-sm mb-4 pb-3"
                    style="color: var(--theme-body-text); border-bottom: 2px solid var(--theme-primary);">
                    <span style="color: var(--theme-primary);">■</span> Latest
                </h4>
                <ul class="space-y-4">
                    @foreach(($latestPosts ?? collect())->take(5) as $lp)
                        <li class="flex gap-3 group">
                            @if($lp->featured_image)
                                <a href="{{ route('tenant.post', ['slug' => $lp->slug, 'locale' => $locale]) }}" class="w-20 h-16 shrink-0 overflow-hidden block">
                                    <img src="{{ $lp->featured_image }}"
                                         alt="{{ $lp->translate('title', $locale) }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                                </a>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h5 class="font-semibold text-sm leading-snug group-hover:opacity-80 transition-opacity" style="color: var(--theme-body-text);">
                                    <a href="{{ route('tenant.post', ['slug' => $lp->slug, 'locale' => $locale]) }}">
                                        {{ $lp->translate('title', $locale) }}
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

            <div class="p-5 text-center" style="background: var(--theme-primary); color: white;">
                <h4 class="font-heading font-black text-xl mb-2">Stay Informed</h4>
                <p class="text-xs opacity-90 mb-4 leading-relaxed">
                    Get the top stories straight to your inbox.
                </p>
                <form action="{{ route('tenant.newsletter.subscribe') }}" method="POST" class="space-y-2 text-left">
                    @csrf
                    <input type="email" name="email" required placeholder="your@email.com"
                           class="w-full px-3 py-2 text-sm" style="background: white; color: #0f172a;">
                    <button type="submit" class="w-full py-2 text-xs font-black uppercase tracking-widest"
                            style="background: #0f172a; color: white;">Sign Up</button>
                </form>
            </div>

            @php($sideAd = \App\Services\AdRendererService::render('sidebar'))
            @if(trim(strip_tags($sideAd)))
                <div class="p-4 text-center" style="background: var(--theme-section-bg); border:1px dashed var(--theme-border-color);">
                    <span class="text-[10px] uppercase tracking-widest font-semibold" style="color: var(--theme-muted);">Advertisement</span>
                    <div class="mt-2">{!! $sideAd !!}</div>
                </div>
            @endif
        </aside>
    </article>
</x-tenant-layout>
