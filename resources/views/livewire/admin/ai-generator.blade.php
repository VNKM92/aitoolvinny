<div class="space-y-6">
    <!-- Header banner -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-slate-900 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight flex items-center">
                <svg class="h-6 w-6 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                AI Content Suite
            </h2>
            <p class="text-xs text-slate-400 mt-1">Generate high-converting blog posts, outlines, YouTube scripts, and social media captions dynamically using Gemini AI.</p>
        </div>
    </div>

    <!-- Main Workspace -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <!-- Left Sidebar - Tabs -->
        <div class="lg:col-span-1 space-y-2">
            <div class="bg-slate-900/40 backdrop-blur-md border border-slate-900 rounded-2xl p-4 space-y-1">
                <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest px-3 mb-2">Available Modules</span>
                <nav class="space-y-1">
                    @foreach($tabs as $key => $label)
                        <button wire:click="selectTab('{{ $key }}')" 
                            class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold flex items-center transition-all duration-200 {{ $activeTab === $key ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400 hover:bg-slate-900/60 hover:text-white' }}">
                            <span class="truncate">{{ $label }}</span>
                        </button>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- Right Pane - Inputs & Result Output -->
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-slate-900/40 backdrop-blur-md border border-slate-900 p-6 rounded-2xl space-y-6">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center">
                    Generate {{ $tabs[$activeTab] }}
                </h3>

                <form wire:submit.prevent="generate" class="space-y-4">
                    <!-- Dynamic Topic Input Field -->
                    @if($this->requiresTopic())
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Topic / Title Description</label>
                            <input wire:model="topic" type="text" 
                                class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-slate-600" 
                                placeholder="Enter a descriptive topic (e.g. 10 Tips for Learning Laravel in 2026)" required>
                            @error('topic') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- Optional Outline Input Field for Article Generator -->
                    @if($activeTab === 'articles')
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Article Outline (Optional)</label>
                            <textarea wire:model="outlineInput" rows="3"
                                class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-slate-600" 
                                placeholder="Provide heading structures or leave blank for auto-outline generation..."></textarea>
                        </div>
                    @endif

                    <!-- Dynamic Content Input Field -->
                    @if($this->requiresContent())
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Base Content / Article Text</label>
                            <textarea wire:model="contentInput" rows="6"
                                class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-slate-600" 
                                placeholder="Paste the content text here to generate descriptions, tags, or alt text..." required></textarea>
                            @error('contentInput') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- Generation Button with loading indicators -->
                    <div class="flex items-center space-x-4">
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 rounded-xl text-xs font-semibold text-white transition-colors shadow-lg shadow-indigo-600/10 flex items-center">
                            <span wire:loading.remove wire:target="generate">
                                Generate Content
                            </span>
                            <span wire:loading wire:target="generate" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Generating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Generated Output Result -->
            <div class="bg-slate-900/40 backdrop-blur-md border border-slate-900 p-6 rounded-2xl space-y-4" x-data="{ copied: false }">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-white uppercase tracking-wider">AI Generation Output</span>
                    @if(!empty($result) && !str_starts_with($result, 'Error:'))
                        <button @click="
                            navigator.clipboard.writeText($refs.outputText.value); 
                            copied = true; 
                            setTimeout(() => copied = false, 2000)
                        " class="px-3 py-1.5 bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white rounded-lg text-[10px] font-semibold transition-all flex items-center">
                            <svg x-show="!copied" class="h-3 w-3 mr-1 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                            </svg>
                            <svg x-show="copied" class="h-3 w-3 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span x-text="copied ? 'Copied!' : 'Copy to Clipboard'"></span>
                        </button>
                    @endif
                </div>

                <div class="relative">
                    @if($isGenerating)
                        <div class="flex flex-col items-center justify-center py-20 space-y-3 bg-slate-950/60 border border-slate-900 rounded-xl">
                            <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                            <span class="text-xs font-semibold text-slate-400">Gemini is generating your output...</span>
                        </div>
                    @else
                        <textarea x-ref="outputText" readonly rows="12"
                            class="block w-full p-4 bg-slate-950 border border-slate-900 rounded-xl text-xs text-slate-300 focus:outline-none font-mono leading-relaxed resize-y"
                            placeholder="Your generated content will appear here...">{{ $result }}</textarea>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
