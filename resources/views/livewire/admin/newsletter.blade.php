<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white">Newsletter Campaigns</h1>
            <p class="text-slate-400 mt-1">Manage newsletter subscribers and send campaign broadcasts.</p>
        </div>
        <button wire:click="toggleCompose" 
            class="px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors shadow-lg shadow-indigo-600/10">
            {{ $isComposing ? 'View Subscriber List' : 'Compose Newsletter' }}
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-rose-950/20 border border-rose-900/30 text-rose-400 rounded-xl text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    @if($isComposing)
        <!-- Compose Newsletter Campaign -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-8 rounded-2xl shadow-2xl max-w-2xl mx-auto">
            <h2 class="text-xl font-bold text-white mb-6">Compose Newsletter Broadcast</h2>
            
            <form wire:submit.prevent="sendBroadcast" class="space-y-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Email Subject</label>
                    <input wire:model="subject" type="text" 
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white placeholder-slate-655 focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                        placeholder="Weekly developer news digest" required>
                    @error('subject') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Newsletter Body (HTML supported)</label>
                    <textarea wire:model="body" rows="10" 
                        class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-white placeholder-slate-655 focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                        placeholder="Compose newsletter content here..." required></textarea>
                    @error('body') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" wire:click="toggleCompose" 
                        class="px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-lg text-slate-400 hover:text-white transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-white font-semibold transition-colors shadow-lg shadow-indigo-600/10">
                        Send Broadcast Campaign
                    </button>
                </div>
            </form>
        </div>
    @else
        <!-- Subscribers Management -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
            <div class="px-6 py-4 bg-slate-950/40 border-b border-slate-800/60 flex items-center justify-between">
                <input wire:model.live="search" type="text" 
                    class="block w-full max-w-xs px-4 py-2 bg-slate-900 border border-slate-800 rounded-lg text-white placeholder-slate-655 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" 
                    placeholder="Search subscribers...">
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase tracking-wider bg-slate-950/40">
                            <th class="px-6 py-4">Subscriber Email</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Opt-in Date</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($subscribers as $sub)
                            <tr class="hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-4 text-sm font-semibold text-white">
                                    {{ $sub->email }}
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="toggleSubscriberStatus({{ $sub->id }})" 
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border transition-all cursor-pointer {{ $sub->is_active ? 'bg-emerald-950/20 text-emerald-400 border-emerald-900/55' : 'bg-rose-950/20 text-rose-400 border-rose-900/55' }}">
                                        {{ $sub->is_active ? 'Active' : 'Unsubscribed' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400">
                                    {{ $sub->created_at->format('M d, Y @ H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-3 text-sm font-medium">
                                    <button onclick="confirm('Remove this subscriber from your list?') || event.stopImmediatePropagation()"
                                        wire:click="deleteSubscriber({{ $sub->id }})" 
                                        class="text-rose-500 hover:text-rose-455 font-semibold transition-colors">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                    No subscribers found in this list.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-slate-950/20 border-t border-slate-800/40">
                {{ $subscribers->links() }}
            </div>
        </div>
    @endif
</div>
