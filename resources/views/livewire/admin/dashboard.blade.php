<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-white">CMS Dashboard</h1>
        <p class="text-slate-400 mt-1">Hello, {{ auth()->user()->name }}. Welcome back to your administration dashboard.</p>
    </div>

    <!-- Blog Admin Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Posts -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Blog Posts</p>
                <h3 class="text-4xl font-extrabold text-white mt-2">{{ $totalPosts }}</h3>
                <a href="{{ route('admin.posts') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 mt-3 inline-flex items-center">
                    Write Post
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
                    Manage
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
                    Edit Pages
                </a>
            </div>
            <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-xl">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>

        <!-- Comments -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Comments</p>
                <h3 class="text-4xl font-extrabold text-white mt-2">{{ $totalComments }}</h3>
                <a href="{{ route('admin.comments') }}" class="text-xs font-semibold text-amber-400 hover:text-amber-300 mt-3 inline-flex items-center">
                    Approve Feed
                </a>
            </div>
            <div class="p-3 bg-amber-500/10 text-amber-400 rounded-xl">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                </svg>
            </div>
        </div>

        <!-- Subscribers -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Subscribers</p>
                <h3 class="text-4xl font-extrabold text-white mt-2">{{ $totalSubscribers }}</h3>
                <a href="{{ route('admin.newsletter') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 mt-3 inline-flex items-center">
                    Campaigns
                </a>
            </div>
            <div class="p-3 bg-indigo-505/10 text-indigo-450 rounded-xl">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- AdSense & SEO Status Widget -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl">
            <h2 class="text-lg font-bold text-white mb-2">Google AdSense Status</h2>
            @php
                $adsenseClient = \App\Services\SiteSettings::get('adsense_client_id');
            @endphp
            @if(!empty($adsenseClient))
                <div class="flex items-center space-x-3 text-emerald-400 bg-emerald-950/20 border border-emerald-900/30 px-4 py-3 rounded-lg">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-semibold">Active: Client ID {{ $adsenseClient }}</span>
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
</div>
