<x-tenant-layout :pages="$pages" :locale="$locale" :seo="$seo" :jsonLd="$jsonLd">
    <div class="space-y-8" x-data="{ search: '', category: 'all' }">
        <!-- Hero section -->
        <div class="text-center max-w-2xl mx-auto py-6">
            <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 via-pink-400 to-indigo-500">
                Free Online Utility Tools
            </h1>
            <p class="text-xs text-slate-400 mt-2 sm:mt-3 leading-relaxed">
                Supercharge your workflow with our developer utilities, converters, formatters, and math calculators. All tools process data locally in your browser for 100% security.
            </p>
        </div>

        <!-- Search and filters -->
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between bg-slate-900/40 backdrop-blur-md border border-slate-900 p-4 rounded-2xl max-w-4xl mx-auto">
            <div class="relative w-full sm:max-w-xs">
                <input x-model="search" type="text" 
                    placeholder="Search 20+ free tools..." 
                    class="w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div class="flex flex-wrap gap-2">
                <button @click="category = 'all'" :class="category === 'all' ? 'bg-indigo-600 text-white' : 'bg-slate-950 text-slate-400 border border-slate-800 hover:text-white'" class="px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase transition-all">
                    All Tools
                </button>
                <button @click="category = 'dev'" :class="category === 'dev' ? 'bg-indigo-600 text-white' : 'bg-slate-950 text-slate-400 border border-slate-800 hover:text-white'" class="px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase transition-all">
                    Developer Tools
                </button>
                <button @click="category = 'text'" :class="category === 'text' ? 'bg-indigo-600 text-white' : 'bg-slate-950 text-slate-400 border border-slate-800 hover:text-white'" class="px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase transition-all">
                    Text Utilities
                </button>
                <button @click="category = 'calc'" :class="category === 'calc' ? 'bg-indigo-600 text-white' : 'bg-slate-950 text-slate-400 border border-slate-800 hover:text-white'" class="px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase transition-all">
                    Calculators
                </button>
            </div>
        </div>

        <!-- Grid of tools -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($tools as $tool)
                @php
                    $isDev = in_array($tool->slug, ['qr-code-generator', 'uuid-generator', 'base64-encoder', 'base64-decoder', 'json-formatter', 'sql-formatter', 'html-formatter', 'css-minifier', 'js-beautifier', 'image-compressor']);
                    $isText = in_array($tool->slug, ['word-counter', 'character-counter', 'slug-generator', 'lorem-ipsum']);
                    $isCalc = in_array($tool->slug, ['password-generator', 'random-password', 'age-calculator', 'emi-calculator', 'gst-calculator', 'percentage-calculator']);
                    
                    $cat = 'unknown';
                    if ($isDev) $cat = 'dev';
                    elseif ($isText) $cat = 'text';
                    elseif ($isCalc) $cat = 'calc';
                @endphp

                <div x-show="(category === 'all' || category === '{{ $cat }}') && (search === '' || '{{ strtolower($tool->translate('name', $locale)) }}'.includes(search.toLowerCase()))" 
                    class="backdrop-blur-md bg-slate-900/40 border border-slate-900 hover:border-slate-850 p-6 rounded-2xl flex flex-col justify-between group transition-all duration-300">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider bg-slate-950 text-indigo-400 border border-indigo-950">
                                @if($isDev) Developer @elseif($isText) Text Utility @else Calculator @endif
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-white group-hover:text-indigo-400 transition-colors">
                            {{ $tool->translate('name', $locale) }}
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-2 line-clamp-3 leading-relaxed">
                            {{ $tool->translate('description', $locale) }}
                        </p>
                    </div>
                    <div class="mt-6 border-t border-slate-900/80 pt-4 flex items-center justify-between">
                        <a href="{{ route('tenant.tools.show', ['slug' => $tool->slug, 'locale' => $locale]) }}" 
                            class="text-[10px] font-bold text-indigo-400 hover:text-indigo-300 transition-colors flex items-center">
                            Open Tool
                            <svg class="h-3 w-3 ml-1 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-tenant-layout>
