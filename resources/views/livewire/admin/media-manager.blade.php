<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-white">Media Manager</h1>
        <p class="text-slate-400 mt-1">Upload and optimize media files, images, and documents.</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Upload & Detail Side panel -->
        <div class="space-y-6">
            <!-- Upload Box -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl">
                <h3 class="text-md font-bold text-white mb-4">Upload File</h3>
                
                <form wire:submit.prevent="uploadFile" class="space-y-4">
                    <div class="border-2 border-dashed border-slate-800 hover:border-indigo-650 rounded-xl p-4 text-center cursor-pointer transition-colors relative">
                        <input type="file" wire:model="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <svg class="mx-auto h-8 w-8 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs text-slate-400 mt-2 block">Choose file or drag here</span>
                        @if ($file)
                            <span class="text-[10px] text-indigo-400 mt-2 block font-semibold truncate">{{ $file->getClientOriginalName() }}</span>
                        @endif
                    </div>
                    @error('file') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror

                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-white text-sm font-semibold transition-colors shadow-lg shadow-indigo-600/10">
                        <span wire:loading.remove wire:target="file">Upload & Optimize</span>
                        <span wire:loading wire:target="file">Processing File...</span>
                    </button>
                </form>
            </div>

            <!-- Image Detail & Alt Tag Config -->
            @if($selectedMediaId)
                <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl space-y-4">
                    <h3 class="text-md font-bold text-white">Alt Tag Configuration</h3>
                    
                    <form wire:submit.prevent="saveAltText" class="space-y-4">
                        @foreach($supportedLocales as $locale)
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                                    Alt Text ({{ strtoupper($locale) }})
                                </label>
                                <input wire:model="alt_text.{{ $locale }}" type="text" 
                                    class="mt-1.5 block w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                                    placeholder="Alt tag descriptions">
                            </div>
                        @endforeach

                        <div class="flex space-x-2 pt-2">
                            <button type="button" wire:click="$set('selectedMediaId', null)" 
                                class="w-1/2 py-2 bg-slate-950 border border-slate-800 rounded-lg text-slate-400 text-xs transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                class="w-1/2 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-white text-xs font-semibold transition-colors">
                                Save Alt
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- Files Directory Grid -->
        <div class="lg:col-span-3 space-y-6">
            <div class="flex items-center justify-between">
                <input wire:model.live="search" type="text" 
                    class="block w-full max-w-xs px-4 py-2 bg-slate-900 border border-slate-800 rounded-lg text-white placeholder-slate-655 focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                    placeholder="Search file catalog...">
            </div>

            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                    @forelse($mediaItems as $media)
                        <div class="bg-slate-950 border border-slate-900 rounded-xl overflow-hidden hover:border-slate-800 transition-all flex flex-col justify-between group relative">
                            <!-- File Preview Area -->
                            <div class="h-32 bg-slate-950 flex items-center justify-center overflow-hidden border-b border-slate-900 relative">
                                @if(str_starts_with($media->file_type, 'image/'))
                                    <img src="{{ asset('storage/' . $media->filepath) }}" alt="Thumbnail" class="w-full h-full object-cover">
                                @else
                                    <svg class="h-10 w-10 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                @endif
                                
                                <!-- Copy url floating toggle -->
                                <button onclick="navigator.clipboard.writeText('{{ asset('storage/' . $media->filepath) }}'); alert('File URL copied to clipboard!');"
                                    class="absolute bottom-2 right-2 p-1.5 bg-slate-900/90 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-white rounded-lg transition-all opacity-0 group-hover:opacity-100" 
                                    title="Copy URL">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Details -->
                            <div class="p-3">
                                <div class="text-xs font-semibold text-slate-200 truncate" title="{{ $media->filename }}">{{ $media->filename }}</div>
                                <div class="text-[10px] text-slate-500 mt-1 flex justify-between">
                                    <span>{{ $media->file_type === 'image/webp' ? 'WebP' : 'Asset' }}</span>
                                    <span>{{ round($media->file_size / 1024, 1) }} KB</span>
                                </div>
                            </div>

                            <!-- Hover Overlay Actions -->
                            <div class="px-3 py-2 bg-slate-950 border-t border-slate-900 flex justify-between text-[10px] font-semibold">
                                <button wire:click="selectMedia({{ $media->id }})" class="text-indigo-400 hover:text-indigo-300">Edit Alt</button>
                                <button onclick="confirm('Delete this file permanently?') || event.stopImmediatePropagation()"
                                    wire:click="deleteMedia({{ $media->id }})" class="text-rose-500 hover:text-rose-455">Delete</button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-4 py-12 text-center text-slate-600">
                            No media files stored yet. Drag files into the uploader block.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
