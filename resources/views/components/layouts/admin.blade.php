<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Control Center' }} | SaaS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full flex overflow-hidden bg-slate-950 text-slate-100">
    <!-- Sidebar -->
    <div class="hidden md:flex md:flex-shrink-0">
        <div class="flex flex-col w-64 border-r border-slate-900 bg-slate-950">
            <div class="flex items-center h-16 px-6 border-b border-slate-900 bg-slate-950">
                <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-pink-500">
                    Control Center
                </span>
            </div>
            
            <div class="flex flex-col flex-1 overflow-y-auto mt-4 px-4 space-y-1">
                @if(auth()->user()->isSuperAdmin())
                    <!-- Super Admin Menu -->
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-2">SaaS System</div>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-slate-900 text-slate-300 hover:text-white transition-colors duration-150">
                        <svg class="mr-3 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                        Websites
                    </a>
                @else
                    <!-- Tenant Admin Menu -->
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-2">CMS Website</div>
                    <div class="px-3 py-2 mb-3 bg-indigo-950/20 border border-indigo-900/30 rounded-lg">
                        <div class="text-xs text-indigo-400 font-semibold">Active Website</div>
                        <div class="text-sm font-bold text-white truncate">{{ $currentTenant->name ?? 'Loading...' }}</div>
                    </div>

                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-slate-900 text-slate-300 hover:text-white transition-colors duration-150">
                        <svg class="mr-3 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.categories') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-slate-900 text-slate-300 hover:text-white transition-colors duration-150">
                        <svg class="mr-3 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Categories
                    </a>
                    <a href="{{ route('admin.posts') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-slate-900 text-slate-300 hover:text-white transition-colors duration-150">
                        <svg class="mr-3 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 00-2-2m2 2v10a2 2 0 01-2 2M9 9h6m-6 4h6m-6 4h3" />
                        </svg>
                        Posts
                    </a>
                    <a href="{{ route('admin.pages') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-slate-900 text-slate-300 hover:text-white transition-colors duration-150">
                        <svg class="mr-3 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Pages
                    </a>
                    <a href="{{ route('admin.settings') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-slate-900 text-slate-300 hover:text-white transition-colors duration-150">
                        <svg class="mr-3 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </a>
                @endif
            </div>

            <!-- User bar -->
            <div class="flex-shrink-0 flex border-t border-slate-900 p-4">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center min-w-0">
                        <div class="h-9 w-9 rounded-full bg-indigo-600 flex items-center justify-center font-bold text-white uppercase text-sm">
                            {{ substr(auth()->user()->name, 0, 2) }}
                        </div>
                        <div class="ml-3 min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    
                    <a href="{{ route('admin.logout') }}" class="text-slate-400 hover:text-rose-500 p-1 rounded hover:bg-slate-900 transition-colors" title="Log Out">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex flex-col w-0 flex-1 overflow-hidden">
        <!-- Top bar (mobile menu toggles here if needed) -->
        <header class="h-16 border-b border-slate-900 bg-slate-950 flex items-center justify-between px-6 z-10">
            <div class="flex items-center">
                <!-- Mobile menu button -->
                <button class="md:hidden text-slate-400 hover:text-white mr-4 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="text-sm text-slate-400 font-medium">
                    Role: <span class="text-indigo-400 capitalize font-semibold">{{ str_replace('_', ' ', auth()->user()->role) }}</span>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                @if(isset($currentTenant) && auth()->user()->isTenantAdmin())
                    <a href="{{ route('tenant.home', ['locale' => $currentTenant->default_locale]) }}" target="_blank" class="flex items-center text-xs font-semibold px-3 py-1.5 bg-slate-900 border border-slate-800 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-all">
                        View Site
                        <svg class="ml-1.5 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                @endif
            </div>
        </header>

        <!-- Dynamic Page View Slot -->
        <main class="flex-1 relative overflow-y-auto focus:outline-none bg-slate-950/60 p-6 md:p-8">
            <div class="max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
