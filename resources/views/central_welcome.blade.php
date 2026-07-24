<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaaS Multi-Tenant CMS | control Center</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full relative overflow-hidden flex flex-col justify-between">
    <!-- Glowing background accents -->
    <div class="absolute top-[-20%] left-[-10%] w-[60%] h-[60%] rounded-full bg-indigo-900/20 blur-[130px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-pink-900/20 blur-[130px] pointer-events-none"></div>

    <!-- Header / Nav -->
    <header class="relative z-10 max-w-7xl mx-auto w-full px-6 py-6 flex items-center justify-between">
        <span class="text-2xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-pink-500">
            VK SaaS CMS
        </span>
        <a href="{{ route('login') }}" 
            class="px-4 py-2 text-sm font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white transition-all shadow-lg shadow-indigo-600/10">
            Control Panel Login
        </a>
    </header>

    <!-- Main Hero -->
    <main class="relative z-10 flex-1 flex items-center justify-center px-6">
        <div class="max-w-4xl text-center space-y-8">
            <div class="inline-flex items-center space-x-2 px-3 py-1 bg-indigo-950/40 border border-indigo-900/50 rounded-full">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-pulse"></span>
                <span class="text-xs text-indigo-300 font-semibold tracking-wider uppercase">Laravel 12 Multi-Tenant Engine</span>
            </div>

            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-white leading-tight">
                Unlimited Websites. <br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-500">
                    One Single Dashboard.
                </span>
            </h1>

            <p class="text-slate-400 max-w-2xl mx-auto text-lg sm:text-xl font-light">
                Deploy production-grade, SEO-optimized, multilingual, and Google AdSense ready websites dynamically in seconds.
            </p>

            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                <a href="{{ route('login') }}" 
                    class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-bold rounded-xl transition-all shadow-xl shadow-indigo-600/20 active:scale-[0.98]">
                    Launch Dashboard
                </a>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-12 border-t border-slate-900 max-w-3xl mx-auto">
                <div class="p-4 bg-slate-900/30 border border-slate-900 rounded-xl">
                    <h3 class="font-bold text-white text-md">Multi-Tenant</h3>
                    <p class="text-xs text-slate-500 mt-1">Shared schema scaling</p>
                </div>
                <div class="p-4 bg-slate-900/30 border border-slate-900 rounded-xl">
                    <h3 class="font-bold text-white text-md">Multi-Domain</h3>
                    <p class="text-xs text-slate-500 mt-1">Custom site host mappings</p>
                </div>
                <div class="p-4 bg-slate-900/30 border border-slate-900 rounded-xl">
                    <h3 class="font-bold text-white text-md">Multi-Language</h3>
                    <p class="text-xs text-slate-500 mt-1">Dynamic JSON translations</p>
                </div>
                <div class="p-4 bg-slate-900/30 border border-slate-900 rounded-xl">
                    <h3 class="font-bold text-white text-md">AdSense Ready</h3>
                    <p class="text-xs text-slate-500 mt-1">Monetize automatically</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 text-center py-6 text-xs text-slate-600">
        &copy; {{ date('Y') }} VK SaaS CMS. All rights reserved. Powered by Laravel 12 & PHP 8.4.
    </footer>
</body>
</html>
