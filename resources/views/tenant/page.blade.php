<x-tenant-layout :tenant="$tenant" :pages="$pages" :locale="$locale" :seo="$seo" :jsonLd="$jsonLd">
    <div class="max-w-3xl mx-auto backdrop-blur-xl bg-slate-900/20 border border-slate-900 rounded-2xl p-6 md:p-10 space-y-6">
        <h1 class="text-3xl md:text-5xl font-extrabold text-white leading-tight">
            {{ $page->title[$locale] ?? reset($page->title) }}
        </h1>
        
        <div class="border-t border-slate-900/80 pt-6 prose prose-invert prose-indigo max-w-none text-slate-350 leading-relaxed text-md">
            {!! nl2br($page->content[$locale] ?? reset($page->content)) !!}
        </div>
    </div>
</x-tenant-layout>
