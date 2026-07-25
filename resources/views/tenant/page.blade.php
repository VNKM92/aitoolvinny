<x-tenant-layout :pages="$pages" :locale="$locale" :seo="$seo" :jsonLd="$jsonLd" :page="$page">
    <div class="news-divider mb-6"></div>

    <nav aria-label="Breadcrumb" class="mb-6 text-xs uppercase tracking-widest" style="color: var(--theme-muted);">
        <ol class="flex flex-wrap gap-2 items-center">
            <li><a href="{{ route('tenant.home', ['locale' => $locale]) }}" class="link-underline font-semibold" style="color: var(--theme-primary);">Home</a></li>
            <li>/</li>
            <li class="truncate max-w-[260px]" style="color: var(--theme-body-text);">
                {{ $page->translate('title', $locale) }}
            </li>
        </ol>
    </nav>

    <article class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <div class="lg:col-span-3">
            <header class="mb-8 pb-6" style="border-bottom: 3px double var(--theme-border-color);">
                <span class="eyebrow font-bold mb-3 inline-block">Page</span>
                <h1 class="font-heading font-black leading-[1.02] tracking-tighter text-3xl md:text-5xl lg:text-[56px]" style="color: var(--theme-body-text);">
                    {{ $page->translate('title', $locale) }}
                </h1>
            </header>

            @if($page->featured_image)
                <figure class="mb-8 news-card overflow-hidden">
                    <img src="{{ $page->featured_image }}"
                         alt="{{ $page->translate('title', $locale) }}"
                         class="w-full h-auto object-cover" loading="eager">
                </figure>
            @endif

            <div class="prose-news max-w-none" id="page-body" style="color: var(--theme-body-text);">
                {!! $page->translate('content', $locale) !!}
            </div>
        </div>

        <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">
            <div class="p-5" style="background: var(--theme-card-bg); border:1px solid var(--theme-border-color);">
                <h4 class="font-heading font-black uppercase tracking-wider text-sm mb-4 pb-3"
                    style="color: var(--theme-body-text); border-bottom: 2px solid var(--theme-primary);">
                    Explore
                </h4>
                <ul class="space-y-3 text-sm font-medium">
                    <li><a href="{{ route('tenant.tools.index', ['locale' => $locale]) }}" class="link-underline" style="color: var(--theme-body-text);">&rarr; Free AI &amp; Web Tools</a></li>
                    <li><a href="{{ route('tenant.home', ['locale' => $locale]) }}" class="link-underline" style="color: var(--theme-body-text);">&rarr; Latest Stories</a></li>
                    <li><a href="{{ route('tenant.sitemap') }}" target="_blank" rel="noopener" class="link-underline" style="color: var(--theme-body-text);">&rarr; Sitemap</a></li>
                    <li><a href="{{ route('tenant.feed') }}" target="_blank" rel="noopener" class="link-underline" style="color: var(--theme-body-text);">&rarr; RSS Feed</a></li>
                </ul>
            </div>

            <div class="p-5" style="background: var(--theme-card-bg); border:1px solid var(--theme-border-color);">
                <h4 class="font-heading font-black uppercase tracking-wider text-sm mb-4 pb-3"
                    style="color: var(--theme-body-text); border-bottom: 2px solid var(--theme-primary);">
                    Latest News
                </h4>
                <ul class="space-y-4">
                    @foreach(($latestPosts ?? collect())->take(4) as $lp)
                        <li class="group">
                            <div class="flex gap-1 mb-1">
                                @if($lp->category)
                                    <span class="text-[9px] uppercase tracking-widest font-bold" style="color: var(--theme-primary);">
                                        {{ $lp->category->translate('name', $locale) }}
                                    </span>
                                @endif
                            </div>
                            <h5 class="font-semibold text-sm leading-snug group-hover:opacity-80 transition-opacity" style="color: var(--theme-body-text);">
                                <a href="{{ route('tenant.post', ['slug' => $lp->slug, 'locale' => $locale]) }}">
                                    {{ $lp->translate('title', $locale) }}
                                </a>
                            </h5>
                            <span class="text-[10px] uppercase tracking-widest mt-1 block" style="color: var(--theme-muted);">
                                {{ $lp->published_at ? $lp->published_at->diffForHumans() : $lp->created_at->diffForHumans() }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="p-5 text-center" style="background: var(--theme-primary); color: white;">
                <h4 class="font-heading font-black text-xl mb-2">Daily Brief</h4>
                <p class="text-xs opacity-90 mb-4 leading-relaxed">
                    One email, all the top headlines.
                </p>
                <form action="{{ route('tenant.newsletter.subscribe') }}" method="POST" class="space-y-2 text-left">
                    @csrf
                    <input type="email" name="email" required placeholder="your@email.com"
                           class="w-full px-3 py-2 text-sm" style="background: white; color: #0f172a;">
                    <button type="submit" class="w-full py-2 text-xs font-black uppercase tracking-widest"
                            style="background: #0f172a; color: white;">Subscribe</button>
                </form>
            </div>
        </aside>
    </article>
</x-tenant-layout>
