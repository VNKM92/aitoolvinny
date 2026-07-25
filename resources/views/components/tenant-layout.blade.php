@props([
    'locale' => null,
    'pages' => [],
    'seo' => [],
    'jsonLd' => '',
    'page' => null,
    'post' => null,
    'bodyClasses' => '',
    'pageOverrides' => null,
])
@php
    $locale = $locale ?? app()->getLocale();
    $rtlLocales = ['ar', 'he', 'fa', 'ur'];
    $direction = in_array($locale, $rtlLocales) ? 'rtl' : 'ltr';

    $siteName = \App\Services\SiteSettings::get('site_name', 'CMS Website');
    $siteLogo = \App\Services\SiteSettings::get('logo', '');
    $adsenseClientId = \App\Services\SiteSettings::get('adsense_client_id', '');
    $customCss = \App\Services\SiteSettings::get('custom_css', '');
    $headerInjection = \App\Services\SiteSettings::get('header_injection', '');
    $footerInjection = \App\Services\SiteSettings::get('footer_injection', '');
    $announcementText = \App\Services\SiteSettings::get('announcement_text', '');
    $supportedLocales = \App\Services\SiteSettings::get('supported_locales', ['en']);

    $pageOrPost = $page ?? $post ?? null;
    if ($pageOverrides === null) {
        $pageOverrides = \App\Services\ThemeService::getPageThemeOverrides($pageOrPost);
    }

    $themeSettings = \App\Services\ThemeService::themeSettings();
    $darkModeStrategy = $themeSettings['theme_dark_mode'] ?? 'auto';
    $initialDarkMode = $darkModeStrategy === 'dark' ? 'true' : 'false';

    $effectiveSettings = \App\Services\ThemeService::getEffectiveThemeSettings($pageOverrides, false);
    $effectiveDarkSettings = \App\Services\ThemeService::getEffectiveThemeSettings($pageOverrides, true);

    $bodyExtraClasses = '';
    if (isset($bodyClasses)) {
        $bodyExtraClasses = $bodyClasses;
    }
    $bodyClass = \App\Services\ThemeService::bodyClasses($effectiveSettings, $bodyExtraClasses);
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $direction }}" class="h-full"
      x-data="{
          darkMode: {{ $darkModeStrategy === 'dark' ? 'true' : ($darkModeStrategy === 'auto' ? '(window.matchMedia && window.matchMedia(\'(prefers-color-scheme: dark)\').matches)' : 'false') }},
          darkStrategy: '{{ $darkModeStrategy }}',
          init() {
              if (this.darkStrategy === 'auto') {
                  const mq = window.matchMedia('(prefers-color-scheme: dark)');
                  this.darkMode = mq.matches;
                  mq.addEventListener('change', (e) => { this.darkMode = e.matches; });
              }
          }
      }"
      :class="darkMode ? 'dark' : 'light'">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

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

    @if(!empty($ga4Id))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $ga4Id }}');
    </script>
    @endif

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="{{ $effectiveSettings['theme_primary'] ?? '#4f46e5' }}">

    @php
        $criticalCss = \App\Services\SiteSettings::get('critical_css', '');
    @endphp
    @if(!empty($criticalCss))
    <style>{!! $criticalCss !!}</style>
    @endif

    <title>{{ $seo['title'] ?? $siteName }}</title>

    <meta name="description" content="{{ $seo['description'] ?? '' }}">
    <link rel="canonical" href="{{ $seo['canonical'] ?? request()->url() }}">

    <meta property="og:title" content="{{ $seo['og']['title'] ?? '' }}">
    <meta property="og:description" content="{{ $seo['og']['description'] ?? '' }}">
    <meta property="og:url" content="{{ $seo['og']['url'] ?? '' }}">
    <meta property="og:type" content="{{ $seo['og']['type'] ?? 'website' }}">
    <meta property="og:image" content="{{ $seo['og']['image'] ?? '' }}">
    <meta property="og:site_name" content="{{ $seo['og']['site_name'] ?? '' }}">
    <meta property="og:locale" content="{{ $locale }}">

    <meta name="twitter:card" content="{{ $seo['twitter']['card'] ?? 'summary_large_image' }}">
    <meta name="twitter:title" content="{{ $seo['twitter']['title'] ?? '' }}">
    <meta name="twitter:description" content="{{ $seo['twitter']['description'] ?? '' }}">
    <meta name="twitter:image" content="{{ $seo['twitter']['image'] ?? '' }}">

    @if(isset($jsonLd))
    <script type="application/ld+json">
        {!! $jsonLd !!}
    </script>
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,700&family=Source+Serif+Pro:ital,wght@0,400;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&family=Roboto:wght@400;500;700&family=Georgia&display=swap" rel="stylesheet">

    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://pagead2.googlesyndication.com">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            {!! \App\Services\ThemeService::cssVariables($effectiveSettings) !!}
        }
        html.dark {
            {!! \App\Services\ThemeService::cssVariables($effectiveDarkSettings) !!}
        }
    </style>

    @if(!empty($adsenseClientId))
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adsenseClientId }}" crossorigin="anonymous"></script>
    @endif

    @if(!empty($customCss))
    <style>
        {!! $customCss !!}
    </style>
    @endif

    @if(!empty($headerInjection))
    {!! $headerInjection !!}
    @endif
</head>
<body class="{{ $bodyClass }} bg-theme-body text-theme-body" style="background-color: var(--theme-body-bg); color: var(--theme-body-text);">

    @php
        $activePopup = \App\Models\Popup::active()->first();
    @endphp
    @if($activePopup)
        <div x-data="{ show: !localStorage.getItem('popup_dismissed_{{ $activePopup->id }}') }"
             x-show="show"
             x-transition
             class="fixed inset-0 z-[60] flex items-center justify-center p-6"
             style="background-color: var(--theme-overlay-color); display: none;">
             <div class="max-w-md w-full p-6 space-y-4" style="background-color: var(--theme-surface-bg); border: 1px solid var(--theme-border-color); border-radius: var(--theme-card-radius); box-shadow: var(--theme-card-shadow);">
                  <h3 class="text-lg font-bold" style="color: var(--theme-body-heading-color);">{{ $activePopup->translate('title', $locale) }}</h3>
                  <div class="text-sm" style="color: var(--theme-body-text);">{!! $activePopup->translate('content', $locale) !!}</div>
                  <div class="flex justify-end pt-2">
                       <button @click="show = false; localStorage.setItem('popup_dismissed_{{ $activePopup->id }}', 'true')"
                               class="btn-primary text-xs">
                             Dismiss
                       </button>
                  </div>
             </div>
        </div>
    @endif

    @if(!empty($announcementText))
        <div class="relative z-30 text-center py-2 text-xs font-bold text-white" style="background-color: var(--theme-primary);">
            {{ $announcementText }}
        </div>
    @endif

    <!-- ================ TOP NEWS TICKER (Lightweight News-Style) ================ -->
    @php
        $latestTickerPosts = app(\App\Services\PostService::class)->getLatestPublished(5);
    @endphp
    @if($latestTickerPosts && $latestTickerPosts->count() > 0)
    <div class="relative z-20 border-b overflow-hidden" style="background-color: var(--theme-body-bg-alt); border-color: var(--theme-border-color);">
        <div class="max-w-7xl mx-auto px-6 h-9 flex items-center">
            <span class="flex-shrink-0 px-3 py-0.5 text-xs font-bold uppercase tracking-wider mr-4 text-white" style="background-color: var(--theme-primary); border-radius: var(--theme-button-radius);">
                Breaking
            </span>
            <div class="relative flex-1 overflow-hidden">
                <div class="whitespace-nowrap animate-[ticker_35s_linear_infinite] flex items-center">
                    @foreach($latestTickerPosts as $tp)
                        <a href="{{ route('tenant.post', ['slug' => $tp->slug, 'locale' => $locale]) }}" class="inline-flex items-center mx-6 text-sm font-semibold hover:underline" style="color: var(--theme-body-heading-color);">
                            <span class="inline-block w-1.5 h-1.5 rounded-full mr-2" style="background-color: var(--theme-primary);"></span>
                            {{ $tp->translate('title', $locale) }}
                        </a>
                    @endforeach
                    @foreach($latestTickerPosts as $tp)
                        <a href="{{ route('tenant.post', ['slug' => $tp->slug, 'locale' => $locale]) }}" class="inline-flex items-center mx-6 text-sm font-semibold hover:underline" style="color: var(--theme-body-heading-color);">
                            <span class="inline-block w-1.5 h-1.5 rounded-full mr-2" style="background-color: var(--theme-primary);"></span>
                            {{ $tp->translate('title', $locale) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ================ MAIN HEADER / NAVIGATION ================ -->
    <header class="relative z-10 border-b bg-header-bg sticky top-0" style="border-color: var(--theme-border-color); backdrop-filter: saturate(180%) blur(12px);">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between gap-6">
            <!-- Branding -->
            <a href="{{ route('tenant.home', ['locale' => $locale]) }}" class="flex items-center space-x-3 flex-shrink-0">
                @if(!empty($siteLogo))
                    <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-9 w-auto" loading="eager">
                @else
                    <span class="text-2xl font-extrabold tracking-tight" style="font-family: var(--theme-font-heading); color: var(--theme-body-heading-color);">
                        <span style="color: var(--theme-primary);">{{ strtoupper(substr($siteName, 0, 1)) }}</span>{{ substr($siteName, 1) }}
                    </span>
                @endif
            </a>

            <!-- Menu -->
            <div class="hidden lg:flex items-center gap-2 flex-1 justify-center flex-wrap">
                <a href="{{ route('tenant.home', ['locale' => $locale]) }}" class="nav-link text-sm px-3 py-2 rounded-theme-btn">Home</a>

                @php
                    $headerCategories = \App\Models\Category::query()->orderBy('id', 'asc')->take(6)->get();
                @endphp
                @foreach($headerCategories as $hc)
                    <a href="{{ route('tenant.category', ['slug' => $hc->slug, 'locale' => $locale]) }}" class="nav-link text-sm px-3 py-2 rounded-theme-btn">
                        {{ $hc->translate('name', $locale) }}
                    </a>
                @endforeach

                @foreach($pages as $p)
                    <a href="{{ route('tenant.page', ['slug' => $p->slug, 'locale' => $locale]) }}" class="nav-link text-sm px-3 py-2 rounded-theme-btn">
                        {{ $p->translate('title', $locale) }}
                    </a>
                @endforeach

                <div class="relative" x-data="{ openTools: false }" @click.outside="openTools = false">
                    <button @click="openTools = !openTools" class="nav-link text-sm px-3 py-2 rounded-theme-btn flex items-center">
                        Free Tools
                        <svg class="h-3 w-3 ml-1 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openTools" x-transition style="display: none;"
                        class="absolute left-0 mt-2 w-60 rounded-theme-card p-2 z-50"
                        style="background-color: var(--theme-surface-bg); border: 1px solid var(--theme-border-color); box-shadow: var(--theme-card-hover-shadow);">
                        <a href="{{ route('tenant.tools.show', ['slug' => 'qr-code-generator', 'locale' => $locale]) }}" class="block px-3 py-2 text-xs rounded-theme-btn nav-link">QR Code Generator</a>
                        <a href="{{ route('tenant.tools.show', ['slug' => 'password-generator', 'locale' => $locale]) }}" class="block px-3 py-2 text-xs rounded-theme-btn nav-link">Password Generator</a>
                        <a href="{{ route('tenant.tools.show', ['slug' => 'json-formatter', 'locale' => $locale]) }}" class="block px-3 py-2 text-xs rounded-theme-btn nav-link">JSON Formatter</a>
                        <a href="{{ route('tenant.tools.show', ['slug' => 'emi-calculator', 'locale' => $locale]) }}" class="block px-3 py-2 text-xs rounded-theme-btn nav-link">EMI Calculator</a>
                        <a href="{{ route('tenant.tools.show', ['slug' => 'image-compressor', 'locale' => $locale]) }}" class="block px-3 py-2 text-xs rounded-theme-btn nav-link">Image Compressor</a>
                        <a href="{{ route('tenant.tools.show', ['slug' => 'word-counter', 'locale' => $locale]) }}" class="block px-3 py-2 text-xs rounded-theme-btn nav-link">Word Counter</a>
                        <a href="{{ route('tenant.tools.show', ['slug' => 'gst-calculator', 'locale' => $locale]) }}" class="block px-3 py-2 text-xs rounded-theme-btn nav-link">GST Calculator</a>
                        <div class="news-divider"></div>
                        <a href="{{ route('tenant.tools.index', ['locale' => $locale]) }}" class="block px-3 py-2 text-xs font-bold uppercase tracking-wider rounded-theme-btn" style="color: var(--theme-primary);">
                            All Free Tools &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right widgets -->
            <div class="flex items-center gap-3 flex-shrink-0">
                @if($darkModeStrategy !== 'light')
                <button @click="darkMode = !darkMode"
                        class="p-2 transition-colors"
                        style="background-color: var(--theme-section-bg); border: 1px solid var(--theme-border-color); border-radius: var(--theme-button-radius); color: var(--theme-nav-color);"
                        title="Toggle Theme"
                        aria-label="Toggle Theme">
                    <svg x-show="darkMode" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                    <svg x-show="!darkMode" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
                @endif

                @if(isset($supportedLocales) && count($supportedLocales) > 1)
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open"
                                class="flex items-center text-xs font-bold uppercase px-3 py-2 transition-colors"
                                style="background-color: var(--theme-section-bg); border: 1px solid var(--theme-border-color); border-radius: var(--theme-button-radius); color: var(--theme-nav-color);">
                            {{ $locale }}
                            <svg class="ml-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition style="display: none;"
                            class="absolute right-0 mt-2 w-28 py-1 z-30 rounded-theme-card"
                            style="background-color: var(--theme-surface-bg); border: 1px solid var(--theme-border-color); box-shadow: var(--theme-card-shadow);">
                            @foreach($supportedLocales as $lang)
                                <a href="{{ route('tenant.home', ['locale' => $lang]) }}"
                                    class="block px-3 py-1.5 text-xs font-semibold uppercase nav-link transition-colors">
                                    {{ $lang }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Mobile menu button -->
                <button type="button"
                        x-data="{ mobileOpen: false }"
                        @click="document.getElementById('mobile-nav-menu').classList.toggle('hidden'); document.getElementById('mobile-nav-menu').classList.toggle('flex');"
                        class="lg:hidden p-2"
                        style="background-color: var(--theme-section-bg); border: 1px solid var(--theme-border-color); border-radius: var(--theme-button-radius); color: var(--theme-nav-color);"
                        aria-label="Menu">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile nav -->
        <div id="mobile-nav-menu" class="hidden lg:hidden flex-col border-t px-6 py-4 gap-2" style="border-color: var(--theme-border-color); background-color: var(--theme-surface-bg);">
            <a href="{{ route('tenant.home', ['locale' => $locale]) }}" class="nav-link text-sm px-3 py-2 rounded-theme-btn">Home</a>
            @foreach($headerCategories as $hc)
                <a href="{{ route('tenant.category', ['slug' => $hc->slug, 'locale' => $locale]) }}" class="nav-link text-sm px-3 py-2 rounded-theme-btn">
                    {{ $hc->translate('name', $locale) }}
                </a>
            @endforeach
            @foreach($pages as $p)
                <a href="{{ route('tenant.page', ['slug' => $p->slug, 'locale' => $locale]) }}" class="nav-link text-sm px-3 py-2 rounded-theme-btn">
                    {{ $p->translate('title', $locale) }}
                </a>
            @endforeach
            <a href="{{ route('tenant.tools.index', ['locale' => $locale]) }}" class="nav-link text-sm px-3 py-2 rounded-theme-btn font-bold" style="color: var(--theme-primary);">All Free Tools &rarr;</a>
        </div>
    </header>

    <!-- Top Banner Ad -->
    @if(!empty($adsenseClientId) && !empty(\App\Services\SiteSettings::get('adsense_top_slot')))
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-center">
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

    {!! \App\Services\AdRendererService::render('header') !!}

    <!-- Main Container News Layout -->
    <main class="relative z-10 flex-1 w-full" style="background-color: var(--theme-body-bg);">
        <div class="max-w-7xl mx-auto w-full px-6 py-8">
            {{ $slot }}
        </div>
    </main>

    {!! \App\Services\AdRendererService::render('footer') !!}

    <!-- ============ FOOTER (News Style) ============ -->
    <footer class="mt-16 border-t" style="background-color: var(--theme-footer-bg); border-color: var(--theme-border-color);">
        <div class="max-w-7xl mx-auto px-6 pt-12 pb-6">
            <!-- Top row: Brand + newsletter + quick links -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10 pb-10 border-b" style="border-color: rgba(255,255,255,0.08);">
                <div class="md:col-span-5 space-y-4">
                    <h4 class="text-xl font-extrabold tracking-tight" style="font-family: var(--theme-font-heading); color: #fff;">
                        <span style="color: var(--theme-primary);">{{ strtoupper(substr($siteName, 0, 1)) }}</span>{{ substr($siteName, 1) }}
                    </h4>
                    <p class="text-sm leading-relaxed text-footer-text">
                        Your independent source for expert analysis, in-depth features, and breaking developments. Plus, a suite of 20+ free premium web utilities for developers and creators.
                    </p>
                    <form class="flex gap-2 max-w-md" method="POST" action="{{ route('tenant.newsletter.subscribe') }}">
                        @csrf
                        <input type="email" name="email" required placeholder="Your email address"
                               class="flex-1 text-sm"
                               style="background-color: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.12); color: #fff; --theme-form-placeholder: #94a3b8;">
                        <button type="submit" class="whitespace-nowrap text-sm font-bold">Subscribe</button>
                    </form>
                </div>

                <div class="md:col-span-3 space-y-3">
                    <h6 class="font-bold uppercase tracking-wider text-xs pb-2" style="color: #fff; border-bottom: 2px solid var(--theme-primary); display: inline-block;">Categories</h6>
                    <ul class="space-y-2 text-sm">
                        @foreach(\App\Models\Category::query()->orderBy('id', 'asc')->take(6)->get() as $fc)
                            <li>
                                <a href="{{ route('tenant.category', ['slug' => $fc->slug, 'locale' => $locale]) }}" class="text-footer-text hover:text-white transition-colors">
                                    {{ $fc->translate('name', $locale) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="md:col-span-4 space-y-3">
                    <h6 class="font-bold uppercase tracking-wider text-xs pb-2" style="color: #fff; border-bottom: 2px solid var(--theme-primary); display: inline-block;">Popular Free Tools</h6>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                        @php
                            $footerTools = [
                                'qr-code-generator' => 'QR Code Generator',
                                'password-generator' => 'Password Generator',
                                'json-formatter' => 'JSON Formatter',
                                'uuid-generator' => 'UUID Generator',
                                'emi-calculator' => 'EMI Calculator',
                                'gst-calculator' => 'GST Calculator',
                                'image-compressor' => 'Image Compressor',
                                'word-counter' => 'Word Counter',
                            ];
                        @endphp
                        @foreach($footerTools as $slug => $label)
                            <a href="{{ route('tenant.tools.show', ['slug' => $slug, 'locale' => $locale]) }}" class="text-footer-text hover:text-white transition-colors">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Secondary row: pages + resources -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 py-6">
                <div class="md:col-span-8 flex flex-wrap gap-x-6 gap-y-2 text-sm">
                    <a href="{{ route('tenant.tools.index', ['locale' => $locale]) }}" class="font-semibold transition-colors" style="color: var(--theme-primary);">All Free Web Utilities</a>
                    @foreach($pages as $p)
                        <a href="{{ route('tenant.page', ['slug' => $p->slug, 'locale' => $locale]) }}" class="text-footer-text hover:text-white transition-colors">
                            {{ $p->translate('title', $locale) }}
                        </a>
                    @endforeach
                </div>
                <div class="md:col-span-4 text-sm text-footer-text md:text-right">
                    Contact: <a href="mailto:hello@{{ parse_url(config('app.url'), PHP_URL_HOST) ?? 'example.com' }}" class="hover:text-white transition-colors">hello@{{ parse_url(config('app.url'), PHP_URL_HOST) ?? 'example.com' }}</a>
                </div>
            </div>

            <!-- Bottom line -->
            <div class="flex flex-col sm:flex-row items-center justify-between text-xs pt-6 border-t text-footer-text" style="border-color: rgba(255,255,255,0.08);">
                <div>
                    &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
                </div>
                <div class="flex gap-5 mt-4 sm:mt-0">
                    <a href="{{ route('tenant.home', ['locale' => $locale]) }}" class="hover:text-white transition-colors">Home</a>
                    <a href="{{ route('tenant.sitemap') }}" class="hover:text-white transition-colors">Sitemap</a>
                    <a href="{{ route('tenant.feed') }}" class="hover:text-white transition-colors">RSS Feed</a>
                </div>
            </div>
        </div>
    </footer>

    @if(!empty($footerInjection))
    {!! $footerInjection !!}
    @endif

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then((reg) => {
                    console.log('ServiceWorker registered', reg.scope);
                }).catch((err) => {
                    console.warn('ServiceWorker registration failed', err);
                });
            });
        }
    </script>

    @php
        $stickyAd = \App\Services\AdRendererService::render('sticky');
        $anchorAd = \App\Services\AdRendererService::render('anchor');
    @endphp
    @if(!empty(trim(strip_tags($stickyAd, '<img><ins><iframe><a><script><div>'))))
        <div class="fixed bottom-0 left-0 w-full z-40 py-2 flex justify-center border-t"
             style="background-color: var(--theme-footer-bg); border-color: var(--theme-border-color);">
            {!! $stickyAd !!}
        </div>
    @endif
    @if(!empty(trim(strip_tags($anchorAd, '<img><ins><iframe><a><script><div>'))))
        <div class="fixed bottom-14 left-1/2 -translate-x-1/2 z-40 md:hidden px-4 py-2 shadow-2xl rounded-theme-card"
             style="background-color: var(--theme-footer-bg); border: 1px solid var(--theme-border-color);">
            {!! $anchorAd !!}
        </div>
    @endif

    <style>
        @keyframes ticker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        @media (prefers-reduced-motion: reduce) {
            .animate-\[ticker_35s_linear_infinite\] { animation: none; }
        }
    </style>
</body>
</html>
