<x-tenant-layout :pages="$pages" :locale="$locale" :seo="$seo" :jsonLd="$jsonLd">
    <article class="max-w-3xl mx-auto space-y-6">
        <!-- Category & Time -->
        <div class="flex items-center space-x-3 text-xs font-semibold uppercase tracking-wider">
            @if($post->category)
                <a href="{{ route('tenant.category', ['slug' => $post->category->slug, 'locale' => $locale]) }}" class="text-pink-500 hover:underline">
                    {{ $post->category->name[$locale] ?? reset($post->category->name) }}
                </a>
                <span class="text-slate-700">|</span>
            @endif
            <span class="text-slate-500">Published {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
        </div>

        <!-- Headline -->
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
            {{ $post->title[$locale] ?? reset($post->title) }}
        </h1>

        <!-- Featured Image -->
        @if($post->featured_image)
            <div class="h-64 sm:h-96 w-full overflow-hidden rounded-2xl border border-slate-900">
                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                    alt="{{ $post->title[$locale] ?? reset($post->title) }}" 
                    class="w-full h-full object-cover">
            </div>
        @endif

        <!-- Post Content Top Ad Placement -->
        {!! \App\Services\AdRendererService::render('post_top') !!}

        <!-- Article Content -->
        <div class="prose prose-invert prose-indigo max-w-none text-slate-300 text-sm leading-relaxed space-y-4 pt-4">
            {!! \App\Services\SEOHTMLOptimizer::optimize(\App\Services\SEOInternalLinker::link($post->content[$locale] ?? reset($post->content))) !!}
        </div>

        <!-- Post Content Bottom Ad Placement -->
        {!! \App\Services\AdRendererService::render('post_bottom') !!}

        <!-- Tags List block -->
        @if($post->tags->count() > 0)
            <div class="flex flex-wrap gap-2 pt-6 border-t border-slate-900">
                @foreach($post->tags as $tag)
                    <span class="inline-flex items-center px-3 py-1 bg-slate-900 border border-slate-800 rounded-lg text-xs font-semibold text-slate-400">
                        #{{ $tag->name[$locale] ?? reset($tag->name) }}
                    </span>
                @endforeach
            </div>
        @endif

        <!-- Google AdSense ad slot inside post -->
        @php
            $adsenseClient = \App\Services\SiteSettings::get('adsense_client_id');
            $adsenseArticleSlot = \App\Services\SiteSettings::get('adsense_article_slot');
        @endphp
        @if(!empty($adsenseClient) && !empty($adsenseArticleSlot) && $post->adsense_enabled)
            <div class="py-6 flex justify-center border-t border-b border-slate-900/60 my-8">
                <!-- AdSense Article Placement -->
                <ins class="adsbygoogle"
                     style="display:block; text-align:center;"
                     data-ad-layout="in-article"
                     data-ad-format="fluid"
                     data-ad-client="{{ $adsenseClient }}"
                     data-ad-slot="{{ $adsenseArticleSlot }}"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
        @endif

        <!-- Livewire Comments Widget -->
        <livewire:public.comment-form :postId="$post->id" />
    </article>
</x-tenant-layout>
