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
<html lang="{{ $locale }}" dir="{{ $direction }}" class="h-full text-slate-900 light" x-data="{ darkMode: false }" :class="darkMode ? 'dark' : 'light'">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Search Console & Webmaster Verifications -->
    @php
        $gscCode = \App\Services\SiteSettings::get('google_site_verification', '');
        $bingCode = \App\Services\SiteSettings::get('msvalidate_01', '');
        $yandexCode = \App\Services\SiteSettings::get('yandex_verification', '');
        $ga4Id = \App\Services\SiteSettings::get('ga_tracking_id', '');
    @endphp
    @if(!empty($gscCode))
    <meta name="google-site-verification" content="{{ $gscCode }}">
    @endif
    @if(!empty($bingCode))
    <meta name="msvalidate.01" content="{{ $bingCode }}">
    @endif
    @if(!empty($yandexCode))
    <meta name="yandex-verification" content="{{ $yandexCode }}">
    @endif

    <!-- Google Analytics 4 (GA4) -->
    @if(!empty($ga4Id))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $ga4Id }}');
    </script>
    @endif

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">

    <!-- Critical CSS -->
    @php
        $criticalCss = \App\Services\SiteSettings::get('critical_css', '');
    @endphp
    @if(!empty($criticalCss))
    <style>{!! $criticalCss !!}</style>
    @endif

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

    @php
        $themeSettings = \App\Services\ThemeService::themeSettings();
    @endphp
    <style>
        :root {
            {!! \App\Services\ThemeService::cssVariables($themeSettings) !!}
        }
    </style>

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
<body class="min-h-full flex flex-col justify-between relative bg-theme-body text-theme-body overflow-x-hidden transition-colors duration-200">
    <!-- Active Popup campaign -->
    @php
        $activePopup = \App\Models\Popup::active()->first();
    @endphp
    @if($activePopup)
        <div x-data="{ show: !localStorage.getItem('popup_dismissed_{{ $activePopup->id }}') }"
             x-show="show"
             class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-500/10 backdrop-blur-sm"
             style="display: none;">
             <div class="bg-white border border-slate-200 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-slate-900">
                  <h3 class="text-lg font-bold text-slate-900">{{ $activePopup->title[$locale] ?? reset($activePopup->title) }}</h3>
                  <div class="text-sm text-slate-600">{!! $activePopup->content[$locale] ?? reset($activePopup->content) !!}</div>
                  <div class="flex justify-end pt-2">
                       <button @click="show = false; localStorage.setItem('popup_dismissed_{{ $activePopup->id }}', 'true')"
                               class="px-4 py-2 bg-primary hover:bg-primary-hover rounded-lg text-xs font-semibold text-white transition-colors">
                             Dismiss
                       </button>
                  </div>
             </div>
        </div>
    @endif

    <!-- Announcement Bar -->
    @if(!empty($announcementText))
        <div class="bg-primary text-center py-2 text-xs font-bold text-white relative z-20 shadow-md">
            {{ $announcementText }}
        </div>
    @endif

    <!-- Background glow accents -->
    <div class="absolute top-0 right-[-10%] w-[45%] h-[520px] rounded-full bg-primary opacity-20 blur-[140px] pointer-events-none"></div>
    <div class="absolute top-[780px] left-[-12%] w-[45%] h-[520px] rounded-full bg-accent opacity-10 blur-[140px] pointer-events-none"></div>

    <!-- Navigation Header -->
    <nav class="relative z-10 border-b border-slate-200/70 bg-header-bg backdrop-blur-xl shadow-[0_20px_60px_rgba(148,163,184,0.16)] sticky top-0">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="{{ route('tenant.home', ['locale' => $locale]) }}" class="flex items-center space-x-3">
                @if(!empty($siteLogo))
                    <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-8 w-auto">
                @else
                    <span class="text-xl font-bold text-primary">
                        {{ $siteName }}
                    </span>
                @endif
            </a>

            <!-- Menu Links -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('tenant.home', ['locale' => $locale]) }}" class="text-sm font-semibold text-slate-700 hover:text-primary transition-colors">Home</a>
                @foreach($pages as $p)
                    <a href="{{ route('tenant.page', ['slug' => $p->slug, 'locale' => $locale]) }}" class="text-sm font-semibold hover:text-primary transition-colors">
                        {{ $p->title[$locale] ?? reset($p->title) }}
                    </a>
                @endforeach
                
                <!-- Online Tools Dropdown -->
                <div class="relative" x-data="{ openTools: false }" @click.outside="openTools = false">
                    <button @click="openTools = !openTools" class="text-sm font-semibold hover:text-primary transition-colors flex items-center">
                        Free Tools
                        <svg class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openTools" style="display: none;" 
                        class="absolute left-0 mt-2 w-56 rounded-xl bg-white border border-slate-200 shadow-2xl p-2 z-50">
                        <a href="{{ route('tenant.tools.show', ['slug' => 'qr-code-generator', 'locale' => $locale]) }}" class="block px-3 py-2 text-xs text-slate-700 hover:bg-slate-100 hover:text-slate-900 rounded-lg transition-colors font-semibold">QR Code Generator</a>
                        <a href="{{ route('tenant.tools.show', ['slug' => 'password-generator', 'locale' => $locale]) }}" class="block px-3 py-2 text-xs text-slate-700 hover:bg-slate-100 hover:text-slate-900 rounded-lg transition-colors font-semibold">Password Generator</a>
                        <a href="{{ route('tenant.tools.show', ['slug' => 'json-formatter', 'locale' => $locale]) }}" class="block px-3 py-2 text-xs text-slate-700 hover:bg-slate-100 hover:text-slate-900 rounded-lg transition-colors font-semibold">JSON Formatter</a>
                        <a href="{{ route('tenant.tools.show', ['slug' => 'emi-calculator', 'locale' => $locale]) }}" class="block px-3 py-2 text-xs text-slate-700 hover:bg-slate-100 hover:text-slate-900 rounded-lg transition-colors font-semibold">EMI Calculator</a>
                        <a href="{{ route('tenant.tools.show', ['slug' => 'image-compressor', 'locale' => $locale]) }}" class="block px-3 py-2 text-xs text-slate-700 hover:bg-slate-100 hover:text-slate-900 rounded-lg transition-colors font-semibold">Image Compressor</a>
                        <div class="border-t border-slate-200 my-1"></div>
                        <a href="{{ route('tenant.tools.index', ['locale' => $locale]) }}" class="block px-3 py-2 text-xs text-primary hover:bg-slate-100 hover:text-primary-hover rounded-lg transition-colors font-bold uppercase tracking-wider">All 20 Free Tools &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Header widgets (Dark mode & locales) -->
            <div class="flex items-center space-x-4">
                <!-- Dark Mode toggle button -->
                <button @click="darkMode = !darkMode" class="p-1.5 bg-slate-100 border border-slate-200 rounded-lg text-slate-600 hover:text-primary transition-colors" title="Toggle Theme">
                    <svg x-show="darkMode" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                    <svg x-show="!darkMode" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                @if(isset($supportedLocales) && count($supportedLocales) > 1)
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center text-xs font-bold uppercase px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-lg text-slate-600 hover:text-primary transition-colors">
                            {{ $locale }}
                            <svg class="ml-1 h-3 w-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" 
                            class="absolute right-0 mt-2 w-24 bg-white shadow-xl border border-slate-200 rounded-lg py-1 z-20">
                            @foreach($supportedLocales as $lang)
                                <a href="{{ route('tenant.home', ['locale' => $lang]) }}" 
                                    class="block px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 uppercase transition-colors">
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

    <!-- Header Ad Placement -->
    {!! \App\Services\AdRendererService::render('header') !!}

    <!-- Main Container -->
    <div class="relative z-10 flex-1 max-w-7xl mx-auto w-full px-6 py-8">
        {{ $slot }}
    </div>

    <!-- Footer Ad Placement -->
    {!! \App\Services\AdRendererService::render('footer') !!}

    <!-- Footer -->
    <footer class="border-t border-slate-200/70 bg-footer-bg mt-12 py-12">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 border-b border-slate-200/70 pb-8 text-xs">
            <div>
                <h4 class="font-bold text-slate-900 uppercase tracking-wider mb-3">{{ $siteName }}</h4>
                <p class="text-slate-600 leading-relaxed">Your premium SaaS content management solution for automatic SEO internal linking, performance optimizations, and free developer utility tool suites.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 uppercase tracking-wider mb-3">Popular Free Tools</h4>
                <div class="grid grid-cols-2 gap-2 text-slate-600">
                    <a href="{{ route('tenant.tools.show', ['slug' => 'qr-code-generator', 'locale' => $locale]) }}" class="hover:text-white transition-colors">QR Code Generator</a>
                    <a href="{{ route('tenant.tools.show', ['slug' => 'password-generator', 'locale' => $locale]) }}" class="hover:text-white transition-colors">Password Generator</a>
                    <a href="{{ route('tenant.tools.show', ['slug' => 'json-formatter', 'locale' => $locale]) }}" class="hover:text-white transition-colors">JSON Formatter</a>
                    <a href="{{ route('tenant.tools.show', ['slug' => 'uuid-generator', 'locale' => $locale]) }}" class="hover:text-white transition-colors">UUID Generator</a>
                    <a href="{{ route('tenant.tools.show', ['slug' => 'emi-calculator', 'locale' => $locale]) }}" class="hover:text-white transition-colors">EMI Calculator</a>
                    <a href="{{ route('tenant.tools.show', ['slug' => 'image-compressor', 'locale' => $locale]) }}" class="hover:text-white transition-colors">Image Compressor</a>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 uppercase tracking-wider mb-3">Resources</h4>
                <div class="flex flex-col space-y-2 text-slate-600">
                    <a href="{{ route('tenant.tools.index', ['locale' => $locale]) }}" class="hover:text-primary transition-colors font-semibold">All Free Web Utilities</a>
                    @foreach($pages as $p)
                        <a href="{{ route('tenant.page', ['slug' => $p->slug, 'locale' => $locale]) }}" class="hover:text-white transition-colors">
                            {{ $p->title[$locale] ?? reset($p->title) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500">
            <div>
                &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
            </div>
            <div class="flex space-x-4 mt-4 sm:mt-0">
                <a href="{{ route('tenant.home', ['locale' => $locale]) }}" class="hover:text-primary">Home</a>
            </div>
        </div>
    </footer>

    <!-- Footer Injection -->
    @if(!empty($footerInjection))
    {!! $footerInjection !!}
    @endif
    <!-- PWA Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then((reg) => {
                    console.log('ServiceWorker registered successfully', reg.scope);
                }).catch((err) => {
                    console.warn('ServiceWorker registration failed', err);
                });
            });
        }
    </script>
    <!-- Dynamic Sticky Bottom and Mobile Anchor Ads -->
    @php
        $stickyAd = \App\Services\AdRendererService::render('sticky');
        $anchorAd = \App\Services\AdRendererService::render('anchor');
    @endphp
    @if(!empty(trim(strip_tags($stickyAd, '<img><ins><iframe><a><script><div>'))))
        <div class="fixed bottom-0 left-0 w-full z-40 bg-footer-bg/95 border-t border-white/10 flex justify-center py-2">
            {!! $stickyAd !!}
        </div>
    @endif
    @if(!empty(trim(strip_tags($anchorAd, '<img><ins><iframe><a><script><div>'))))
        <div class="fixed bottom-12 left-1/2 transform -translate-x-1/2 z-40 md:hidden bg-footer-bg/95 border border-white/10 rounded-xl px-4 py-2 shadow-2xl">
            {!! $anchorAd !!}
        </div>
    @endif
</body>
</html>
