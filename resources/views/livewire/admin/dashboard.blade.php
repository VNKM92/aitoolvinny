<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-white">Dashboard</h1>
        <p class="text-slate-400 mt-1">Hello, {{ auth()->user()->name }}. Welcome back to your administration dashboard.</p>
    </div>

    @if(auth()->user()->isSuperAdmin())
        <!-- Super Admin View -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Website Stats Card -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Hosted Websites</p>
                    <h3 class="text-4xl font-extrabold text-white mt-2">{{ $totalTenants }}</h3>
                    <a href="{{ route('admin.tenants') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 mt-4 inline-flex items-center">
                        Manage Websites
                        <svg class="ml-1 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div class="p-4 bg-indigo-500/10 text-indigo-400 rounded-2xl">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>

            <!-- Users Stats Card -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tenant Administrators</p>
                    <h3 class="text-4xl font-extrabold text-white mt-2">{{ $totalUsers }}</h3>
                    <p class="text-xs text-slate-500 mt-4">Authorized content editors</p>
                </div>
                <div class="p-4 bg-pink-500/10 text-pink-400 rounded-2xl">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl">
            <h2 class="text-lg font-bold text-white mb-4">Quick SaaS Management Tasks</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('admin.tenants') }}" class="p-4 bg-slate-950 hover:bg-slate-905 border border-slate-800 rounded-xl block transition-all hover:scale-[1.01]">
                    <h4 class="font-semibold text-white">Create New Website</h4>
                    <p class="text-xs text-slate-400 mt-1">Spin up a new client blog or site with subdomains and default locale settings.</p>
                </a>
                <a href="{{ route('admin.tenants') }}" class="p-4 bg-slate-950 hover:bg-slate-905 border border-slate-800 rounded-xl block transition-all hover:scale-[1.01]">
                    <h4 class="font-semibold text-white">Map Custom Domains</h4>
                    <p class="text-xs text-slate-400 mt-1">Bind custom hostname mappings (e.g. clientdomain.com) to active client sites.</p>
                </a>
            </div>
        </div>
    @else
        <!-- Tenant Admin View -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <!-- Posts -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Blog Posts</p>
                    <h3 class="text-4xl font-extrabold text-white mt-2">{{ $totalPosts }}</h3>
                    <a href="{{ route('admin.posts') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 mt-3 inline-flex items-center">
                        Write a post
                    </a>
                </div>
                <div class="p-3 bg-indigo-500/10 text-indigo-400 rounded-xl">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 00-2-2m2 2v10a2 2 0 01-2 2M9 9h6m-6 4h6m-6 4h3" />
                    </svg>
                </div>
            </div>

            <!-- Categories -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Categories</p>
                    <h3 class="text-4xl font-extrabold text-white mt-2">{{ $totalCategories }}</h3>
                    <a href="{{ route('admin.categories') }}" class="text-xs font-semibold text-pink-400 hover:text-pink-300 mt-3 inline-flex items-center">
                        Manage categories
                    </a>
                </div>
                <div class="p-3 bg-pink-500/10 text-pink-400 rounded-xl">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            <!-- Pages -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pages</p>
                    <h3 class="text-4xl font-extrabold text-white mt-2">{{ $totalPages }}</h3>
                    <a href="{{ route('admin.pages') }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 mt-3 inline-flex items-center">
                        Edit static pages
                    </a>
                </div>
                <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-xl">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- AdSense & SEO Status Widget -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl">
                <h2 class="text-lg font-bold text-white mb-2">Google AdSense Status</h2>
                @if(isset($currentTenant) && !empty($currentTenant->settings['adsense_client_id']))
                    <div class="flex items-center space-x-3 text-emerald-400 bg-emerald-950/20 border border-emerald-900/30 px-4 py-3 rounded-lg">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-semibold">Active: Client ID {{ $currentTenant->settings['adsense_client_id'] }}</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-2">Dynamic Ad Placement is enabled in Header, Sidebar and Article spaces.</p>
                @else
                    <div class="flex items-center space-x-3 text-amber-400 bg-amber-950/20 border border-amber-900/30 px-4 py-3 rounded-lg">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-sm font-semibold">AdSense Disabled</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-2">Go to <a href="{{ route('admin.settings') }}" class="text-indigo-400 underline hover:text-indigo-300">Settings</a> to enter your Google AdSense Client ID.</p>
                @endif
            </div>

            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl">
                <h2 class="text-lg font-bold text-white mb-2">SEO & Sitemap</h2>
                <div class="flex items-center justify-between bg-slate-950 border border-slate-800 px-4 py-3 rounded-lg">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sitemap XML</span>
                        <a href="/sitemap.xml" target="_blank" class="text-sm text-indigo-400 hover:text-indigo-300 font-semibold truncate block mt-0.5">/sitemap.xml</a>
                    </div>
                    <span class="text-xs px-2.5 py-1 bg-indigo-950/40 text-indigo-400 font-bold border border-indigo-900/50 rounded-full">Auto Generated</span>
                </div>
                <p class="text-xs text-slate-400 mt-2">Search engine optimization schemas (JSON-LD) are dynamically injected in all posts.</p>
            </div>
        </div>
    @endif
</div>
