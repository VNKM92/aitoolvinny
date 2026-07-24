<x-tenant-layout :pages="$pages" :locale="$locale" :seo="$seo" :jsonLd="$jsonLd">
    <article class="max-w-3xl mx-auto space-y-6">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
            {{ $page->title[$locale] ?? reset($page->title) }}
        </h1>

        @php
            $content = $page->content[$locale] ?? reset($page->content);
            // Splitting string using captures to extract shortcode delimiters
            $segments = preg_split('/(\[faqs\]|\[form=\d+\])/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        @endphp

        <div class="prose prose-invert prose-indigo max-w-none text-slate-300 text-sm leading-relaxed space-y-4 pt-4">
            @foreach($segments as $segment)
                @if($segment === '[faqs]')
                    @include('tenant.widgets.faqs', ['faqs' => \App\Models\Faq::orderBy('order')->get(), 'locale' => $locale])
                @elseif(preg_match('/\[form=(\d+)\]/', $segment, $matches))
                    <livewire:public.custom-form :formId="(int)$matches[1]" />
                @else
                    {!! $segment !!}
                @endif
            @endforeach
        </div>
    </article>
</x-tenant-layout>
