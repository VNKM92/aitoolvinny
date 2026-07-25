<!DOCTYPE html>
@php
    $adminThemeSettings = \App\Services\ThemeService::adminThemeSettings();
    $effectiveAdminBg = $adminThemeSettings['theme_admin_body_bg'] ?? '#f1f5f9';
    $effectiveAdminText = $adminThemeSettings['theme_admin_body_text'] ?? '#0f172a';
    $effectiveSidebarBg = $adminThemeSettings['theme_admin_sidebar_bg'] ?? '#0f172a';
    $effectiveSidebarText = $adminThemeSettings['theme_admin_sidebar_text'] ?? '#cbd5e1';
    $effectiveSidebarActive = $adminThemeSettings['theme_admin_sidebar_active'] ?? '#4f46e5';
    $effectiveSidebarHover = $adminThemeSettings['theme_admin_sidebar_hover'] ?? '#1e293b';
    $effectiveNavbarBg = $adminThemeSettings['theme_admin_navbar_bg'] ?? '#ffffff';
    $effectiveCardsBg = $adminThemeSettings['theme_admin_cards_bg'] ?? '#ffffff';
    $effectiveFormsBg = $adminThemeSettings['theme_admin_forms_bg'] ?? '#f8fafc';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Control Center' }} | CMS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            {!! \App\Services\ThemeService::cssVariables($adminThemeSettings) !!}
        }
        .text-backend-primary { color: var(--theme-backend-primary) !important; }
        .bg-backend-primary { background-color: var(--theme-backend-primary) !important; }
        .hover\:bg-backend-primary-hover:hover { background-color: var(--theme-backend-primary-hover) !important; }
        .hover\:text-backend-primary:hover { color: var(--theme-backend-primary) !important; }
        .focus\:ring-backend:focus { --tw-ring-color: var(--theme-backend-primary) !important; border-color: var(--theme-backend-primary) !important; }
        .sidebar-link { color: var(--theme-admin-sidebar-text); transition: all 150ms ease; }
        .sidebar-link:hover { background-color: var(--theme-admin-sidebar-hover); color: #fff; }
        .sidebar-link.active { background-color: var(--theme-admin-sidebar-active); color: #fff; }
    </style>
</head>
<body class="h-full flex overflow-hidden"
      style="background-color: var(--theme-admin-body-bg); color: var(--theme-admin-body-text);">

    <!-- Sidebar -->
    <div class="hidden md:flex md:flex-shrink-0">
        <div class="flex flex-col w-64 border-r flex flex-col"
             style="background-color: var(--theme-admin-sidebar-bg); border-color: rgba(255,255,255,0.06);">
            <div class="flex items-center h-16 px-6 border-b"
                 style="border-color: rgba(255,255,255,0.06); background-color: var(--theme-admin-sidebar-bg);">
                <span class="text-xl font-extrabold tracking-tight" style="color: var(--theme-backend-primary);">
                    {{ \App\Services\SiteSettings::get('site_name', 'Control Center') }}
                </span>
            </div>

            <div class="flex flex-col flex-1 overflow-y-auto mt-4 px-3 space-y-1 pb-4">
                <div class="text-[11px] font-bold uppercase tracking-widest px-3 py-2"
                     style="color: rgba(203, 213, 225, 0.6);">CMS Navigation</div>

                <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.categories') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Categories
                </a>

                <a href="{{ route('admin.subcategories') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h10M4 18h10" />
                    </svg>
                    Subcategories
                </a>

                <a href="{{ route('admin.posts') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 00-2-2m2 2v10a2 2 0 01-2 2M9 9h6m-6 4h6m-6 4h3" />
                    </svg>
                    Posts
                </a>

                <a href="{{ route('admin.pages') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Pages
                </a>

                <a href="{{ route('admin.media') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Media Manager
                </a>

                <a href="{{ route('admin.comments') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                    Comments
                </a>

                <a href="{{ route('admin.newsletter') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Newsletter
                </a>

                <a href="{{ route('admin.forms') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Form Builder
                </a>

                <a href="{{ route('admin.tools') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                    </svg>
                    Tools
                </a>

                <div class="text-[11px] font-bold uppercase tracking-widest px-3 py-2 mt-3"
                     style="color: rgba(203, 213, 225, 0.6);">AI &amp; Growth</div>

                <a href="{{ route('admin.ai') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    AI Generator
                </a>

                <a href="{{ route('admin.monetization') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Monetization
                </a>

                <a href="{{ route('admin.faqs') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    FAQ Builder
                </a>

                <a href="{{ route('admin.popups') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Popup Manager
                </a>

                <a href="{{ route('admin.logs') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Audit Logs
                </a>

                <a href="{{ route('admin.settings') }}" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium rounded-lg">
                    <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543-.94-3.31.826-2.37 2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>
            </div>

            <div class="flex-shrink-0 flex border-t p-4" style="border-color: rgba(255,255,255,0.06);">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center min-w-0">
                        <div class="h-9 w-9 rounded-full flex items-center justify-center font-bold text-white uppercase text-sm"
                             style="background-color: var(--theme-backend-primary);">
                            {{ substr(auth()->user()->name, 0, 2) }}
                        </div>
                        <div class="ml-3 min-w-0">
                            <p class="text-sm font-semibold truncate" style="color: #fff;">{{ auth()->user()->name }}</p>
                            <p class="text-xs truncate" style="color: rgba(203, 213, 225, 0.6);">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    <a href="{{ route('admin.logout') }}" class="p-1 rounded transition-colors"
                       style="color: rgba(203, 213, 225, 0.6);"
                       onmouseover="this.style.color='#f43f5e'"
                       onmouseout="this.style.color='rgba(203, 213, 225, 0.6)'"
                       title="Log Out">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col w-0 flex-1 overflow-hidden">
        <header class="h-16 border-b flex items-center justify-between px-6 z-10 sticky top-0"
                style="background-color: var(--theme-admin-navbar-bg); border-color: var(--theme-border-color);">
            <div class="flex items-center">
                <button class="md:hidden mr-4 focus:outline-none p-2 rounded-lg"
                        style="color: var(--theme-nav-color); background-color: var(--theme-section-bg);">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="text-sm font-medium" style="color: var(--theme-form-label);">
                    Role: <span class="font-bold capitalize" style="color: var(--theme-backend-primary);">
                        {{ str_replace('_', ' ', auth()->user()->role) }}
                    </span>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <a href="{{ route('tenant.home') }}" target="_blank"
                   class="flex items-center text-xs font-semibold px-3 py-1.5 rounded-lg border transition-all"
                   style="background-color: var(--theme-admin-forms-bg); border-color: var(--theme-border-color); color: var(--theme-body-text);">
                    View Site
                    <svg class="ml-1.5 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
        </header>

        <main class="flex-1 relative overflow-y-auto focus:outline-none p-6 md:p-8"
              style="background-color: var(--theme-admin-body-bg);">
            <div class="max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
