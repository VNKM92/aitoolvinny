<!DOCTYPE html>
@php
    $rtlLocales = ['ar', 'he', 'fa', 'ur'];
    $direction = in_array($locale, $rtlLocales) ? 'rtl' : 'ltr';
    
    // Fetch unified single-site settings
    $siteName = \App\Services\SiteSettings::get('site_name', 'CMS Website');
    $siteLogo = \App\Services\SiteSettings::get('logo', '');
    $adsenseClientId = \App\Services\SiteSettings::get('adsense_client_id', '');
    $customCss = \App\Services\SiteSettings::get('custom_css', '');
    $headerInjection = \App\Services\SiteSettings::get('header_injection', '');
    $footerInjection = \App\Services\SiteSettings::get('footer_injection', '');
    $announcementText = \App\Services\SiteSettings::get('announcement_text', '');
    $supportedLocales = \App\Services\SiteSettings::get('supported_locales', ['en']);
@endphp
<html lang="{{ $locale }}" dir="{{ $direction }}" class="h-full bg-slate-950 text-slate-100" x-data="{ darkMode: true }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $seo['title'] ?? $siteName }}</title>
    
    <!-- Dynamic SEO Meta Tags -->
    <meta name="description" content="{{ $seo['description'] ?? '' }}">
    <link rel="canonical" href="{{ $seo['canonical'] ?? request()->url() }}">
    
    <!-- Open Graph tags -->
    <meta property="og:title" content="{{ $seo['og']['title'] ?? '' }}">
    <meta property="og:description" content="{{ $seo['og']['description'] ?? '' }}">
    <meta property="og:url" content="{{ $seo['og']['url'] ?? '' }}">
    <meta property="og:type" content="{{ $seo['og']['type'] ?? 'website' }}">
    <meta property="og:image" content="{{ $seo['og']['image'] ?? '' }}">
    <meta property="og:site_name" content="{{ $seo['og']['site_name'] ?? '' }}">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="{{ $seo['twitter']['card'] ?? 'summary_large_image' }}">
    <meta name="twitter:title" content="{{ $seo['twitter']['title'] ?? '' }}">
    <meta name="twitter:description" content="{{ $seo['twitter']['description'] ?? '' }}">
    <meta name="twitter:image" content="{{ $seo['twitter']['image'] ?? '' }}">

    <!-- Structured Data (JSON-LD) -->
    @if(isset($jsonLd))
    <script type="application/ld+json">
        {!! $jsonLd !!}
    </script>
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google AdSense script integration -->
    @if(!empty($adsenseClientId))
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adsenseClientId }}" crossorigin="anonymous"></script>
    @endif

    <!-- Dynamic Custom CSS Injection -->
    @if(!empty($customCss))
    <style>
        {!! $customCss !!}
    </style>
    @endif

    <!-- Header Injection -->
    @if(!empty($headerInjection))
    {!! $headerInjection !!}
    @endif
</head>
<body class="min-h-full flex flex-col justify-between relative bg-slate-950 text-slate-100 overflow-x-hidden transition-colors duration-200">
    <!-- Active Popup campaign -->
    @php
        $activePopup = \App\Models\Popup::active()->first();
    @endphp
    @if($activePopup)
        <div x-data="{ show: !localStorage.getItem('popup_dismissed_{{ $activePopup->id }}') }"
             x-show="show"
             class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm"
             style="display: none;">
             <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-slate-100">
                  <h3 class="text-lg font-bold text-white">{{ $activePopup->title[$locale] ?? reset($activePopup->title) }}</h3>
                  <div class="text-sm text-slate-300">{!! $activePopup->content[$locale] ?? reset($activePopup->content) !!}</div>
                  <div class="flex justify-end pt-2">
                       <button @click="show = false; localStorage.setItem('popup_dismissed_{{ $activePopup->id }}', 'true')"
                               class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-xs font-semibold text-white transition-colors">
                             Dismiss
                       </button>
                  </div>
             </div>
        </div>
    @endif

    <!-- Announcement Bar -->
    @if(!empty($announcementText))
        <div class="bg-gradient-to-r from-indigo-650 to-pink-600 text-center py-2 text-xs font-bold text-white relative z-20 shadow-md">
            {{ $announcementText }}
        </div>
    @endif

    <!-- Background glow accents -->
    <div class="absolute top-0 right-[-10%] w-[50%] h-[500px] rounded-full bg-indigo-900/10 blur-[130px] pointer-events-none"></div>
    <div class="absolute top-[800px] left-[-10%] w-[50%] h-[500px] rounded-full bg-pink-900/5 blur-[130px] pointer-events-none"></div>

    <!-- Navigation Header -->
    <nav class="relative z-10 border-b border-slate-900/80 bg-slate-950/80 backdrop-blur-md sticky top-0">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('tenant.home', ['locale' => $locale]) }}" class="flex items-center space-x-3">
                @if(!empty($siteLogo))
                    <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-8 w-auto">
                @else
                    <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-pink-500">
                        {{ $siteName }}
                    </span>
                @endif
            </a>

            <!-- Menu Links -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('tenant.home', ['locale' => $locale]) }}" class="text-sm font-semibold hover:text-indigo-400 transition-colors">Home</a>
                @foreach($pages as $p)
                    <a href="{{ route('tenant.page', ['slug' => $p->slug, 'locale' => $locale]) }}" class="text-sm font-semibold hover:text-indigo-400 transition-colors">
                        {{ $p->title[$locale] ?? reset($p->title) }}
                    </a>
                @endforeach
            </div>

            <!-- Header widgets (Dark mode & locales) -->
            <div class="flex items-center space-x-4">
                <!-- Dark Mode toggle button -->
                <button @click="darkMode = !darkMode" class="p-1.5 bg-slate-900/80 border border-slate-800 rounded-lg text-slate-400 hover:text-white transition-colors" title="Toggle Theme">
                    <svg x-show="darkMode" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                    <svg x-show="!darkMode" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                @if(isset($supportedLocales) && count($supportedLocales) > 1)
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center text-xs font-bold uppercase px-3 py-1.5 bg-slate-900/80 border border-slate-800 rounded-lg text-slate-300 hover:text-white transition-colors">
                            {{ $locale }}
                            <svg class="ml-1 h-3 w-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" 
                            class="absolute right-0 mt-2 w-24 bg-slate-900 border border-slate-800 rounded-lg shadow-xl py-1 z-20">
                            @foreach($supportedLocales as $lang)
                                <a href="{{ route('tenant.home', ['locale' => $lang]) }}" 
                                    class="block px-3 py-1.5 text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white uppercase transition-colors">
                                    {{ $lang }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Top Banner Ad (Google AdSense) -->
    @if(!empty($adsenseClientId) && !empty(\App\Services\SiteSettings::get('adsense_top_slot')))
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-center">
            <!-- AdSense Top Banner Placement -->
            <ins class="adsbygoogle"
                 style="display:block; text-align:center;"
                 data-ad-layout="in-article"
                 data-ad-format="fluid"
                 data-ad-client="{{ $adsenseClientId }}"
                 data-ad-slot="{{ \App\Services\SiteSettings::get('adsense_top_slot') }}"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    @endif

    <!-- Main Container -->
    <div class="relative z-10 flex-1 max-w-7xl mx-auto w-full px-6 py-8">
        {{ $slot }}
    </div>

    <!-- Footer -->
    <footer class="border-t border-slate-900 bg-slate-950 mt-12">
        <div class="max-w-7xl mx-auto px-6 py-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500">
            <div>
                &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
            </div>
            <div class="flex space-x-4 mt-4 sm:mt-0">
                <a href="{{ route('tenant.home', ['locale' => $locale]) }}" class="hover:text-slate-300">Home</a>
                @foreach($pages as $p)
                    <a href="{{ route('tenant.page', ['slug' => $p->slug, 'locale' => $locale]) }}" class="hover:text-slate-300">
                        {{ $p->title[$locale] ?? reset($p->title) }}
                    </a>
                @endforeach
            </div>
        </div>
    </footer>

    <!-- Footer Injection -->
    @if(!empty($footerInjection))
    {!! $footerInjection !!}
    @endif
</body>
</html>
