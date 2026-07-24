<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white">FAQ Builder</h1>
            <p class="text-slate-400 mt-1">Configure accordion FAQ widgets to embed on your website pages.</p>
        </div>
        <button wire:click="toggleCreate" 
            class="px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors shadow-lg shadow-indigo-600/10">
            {{ $isCreating ? 'Back to List' : 'Create FAQ' }}
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @if($isCreating)
            <!-- Create/Edit Form -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl shadow-xl lg:col-span-2">
                <h2 class="text-xl font-bold text-white mb-6">
                    {{ $editingFaqId ? 'Edit FAQ Accordion' : 'Create FAQ Accordion' }}
                </h2>

                <form wire:submit.prevent="saveFaq" class="space-y-6">
                    @foreach($supportedLocales as $locale)
                        <div class="space-y-4 bg-slate-950/20 p-4 rounded-xl border border-slate-850">
                            <div class="text-xs font-bold text-slate-500 uppercase tracking-widest">Language: {{ strtoupper($locale) }}</div>
                            
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Question</label>
                                <input wire:model="questions.{{ $locale }}" type="text" 
                                    class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                                    placeholder="e.g. What is your return policy?" required>
                                @error('questions.' . $locale) <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Answer</label>
                                <textarea wire:model="answers.{{ $locale }}" rows="4"
                                    class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                                    placeholder="We provide 30 days hassle-free returns." required></textarea>
                                @error('answers.' . $locale) <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endforeach

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Sort Order</label>
                        <input wire:model="order" type="number" 
                            class="mt-1.5 block w-full max-w-[150px] px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        @error('order') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" wire:click="toggleCreate" 
                            class="px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-slate-400 hover:text-white transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-white font-semibold transition-colors">
                            Save FAQ Accordion
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- FAQ List Table -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl lg:col-span-3">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase tracking-wider bg-slate-950/40">
                                <th class="px-6 py-4">Sort Order</th>
                                <th class="px-6 py-4">FAQ Item (Translations)</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            @forelse($faqs as $faq)
                                <tr class="hover:bg-slate-900/20 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-400">
                                        <code>#{{ $faq->order }}</code>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-white">
                                            Q: {{ $faq->question[app()->getLocale()] ?? reset($faq->question) }}
                                        </div>
                                        <p class="text-xs text-slate-400 mt-1 truncate max-w-lg">
                                            A: {{ $faq->answer[app()->getLocale()] ?? reset($faq->answer) }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-3 text-sm font-medium">
                                        <button wire:click="editFaq({{ $faq->id }})" 
                                            class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">
                                            Edit
                                        </button>
                                        <button onclick="confirm('Delete this FAQ item?') || event.stopImmediatePropagation()"
                                            wire:click="deleteFaq({{ $faq->id }})" 
                                            class="text-rose-500 hover:text-rose-455 font-semibold transition-colors">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-slate-500">
                                        No FAQ accordions built yet. Click "Create FAQ" to start building.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
