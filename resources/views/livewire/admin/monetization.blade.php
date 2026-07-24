<div class="space-y-6">
    <!-- Header banner -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-slate-900 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight flex items-center">
                <svg class="h-6 w-6 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16v1m-6-16h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2z" />
                </svg>
                Monetization Center
            </h2>
            <p class="text-xs text-slate-400 mt-1">Manage advertising slots, direct deals, Google AdSense slots, and cloaked affiliate keywords.</p>
        </div>
        <div>
            @if($activeTab === 'ads' && !$isCreatingAd)
                <button wire:click="$set('isCreatingAd', true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-semibold text-white transition-colors shadow-lg shadow-indigo-600/10">
                    Create Ad Placement
                </button>
            @elseif($activeTab === 'affiliates' && !$isCreatingAffiliate)
                <button wire:click="$set('isCreatingAffiliate', true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-semibold text-white transition-colors shadow-lg shadow-indigo-600/10">
                    Add Affiliate Link
                </button>
            @endif
        </div>
    </div>

    <!-- Navigation tabs -->
    <div class="flex space-x-1 border-b border-slate-900 pb-px">
        <button wire:click="selectTab('ads')" 
            class="px-4 py-2 text-xs font-bold border-b-2 transition-all duration-200 {{ $activeTab === 'ads' ? 'border-indigo-500 text-white font-bold' : 'border-transparent text-slate-400 hover:text-white' }}">
            Ad Placements (A/B Testing)
        </button>
        <button wire:click="selectTab('affiliates')" 
            class="px-4 py-2 text-xs font-bold border-b-2 transition-all duration-200 {{ $activeTab === 'affiliates' ? 'border-indigo-500 text-white font-bold' : 'border-transparent text-slate-400 hover:text-white' }}">
            Affiliate Links Cloaking
        </button>
    </div>

    <!-- Message Flash Alert -->
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-xl text-xs font-semibold">
            {{ session('message') }}
        </div>
    @endif

    <!-- --- AD PLACEMENTS VIEW --- -->
    @if($activeTab === 'ads')
        @if($isCreatingAd)
            <div class="bg-slate-900/40 backdrop-blur-md border border-slate-900 p-6 rounded-2xl space-y-4">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                    {{ $editingAdId ? 'Edit Ad Placement' : 'Create New Ad Placement' }}
                </h3>
                <form wire:submit.prevent="saveAd" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Ad Placement Name</label>
                            <input wire:model="adName" type="text" class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Header Leaderboard AdSense" required>
                            @error('adName') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Placement Location</label>
                            <select wire:model="adLocation" class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                @foreach($adLocations as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Ad Network / Type</label>
                            <select wire:model="adType" class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="custom">Custom HTML/JS Banner Code (With Click Tracking)</option>
                                <option value="adsense">Google AdSense Tag</option>
                                <option value="manager">Google Ad Manager (DFP)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-450">Destination URL (Optional - For custom ad click tracking)</label>
                            <input wire:model="adDestinationUrl" type="text" class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. https://affiliate.program/click?id=123">
                            @error('adDestinationUrl') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Ad HTML Code / Script</label>
                        <textarea wire:model="adCode" rows="6" class="mt-1.5 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-300 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Paste your AdSense <ins> script, Google Ad Manager tag, or custom banner image HTML code here..." required></textarea>
                        @error('adCode') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center space-x-2 py-2">
                        <label class="flex items-center cursor-pointer select-none">
                            <input wire:model="adIsActive" type="checkbox" class="sr-only peer">
                            <div class="w-5 h-5 bg-slate-950 border border-slate-850 rounded flex items-center justify-center peer-checked:bg-indigo-600 peer-checked:border-indigo-600 mr-2 transition-all">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-xs text-slate-400">Ad is active and available for A/B rendering</span>
                        </label>
                    </div>

                    <div class="flex space-x-3 pt-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-semibold text-white transition-colors">
                            Save Placement
                        </button>
                        <button type="button" wire:click="selectTab('ads')" class="px-4 py-2 bg-slate-950 border border-slate-850 text-slate-400 hover:text-white rounded-xl text-xs font-semibold transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- Placements list -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-850 text-slate-400 font-semibold uppercase tracking-wider bg-slate-950/40">
                                <th class="px-6 py-4">Ad Placement Details</th>
                                <th class="px-6 py-4">Location Slot</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4 text-center">Impressions</th>
                                <th class="px-6 py-4 text-center">Clicks</th>
                                <th class="px-6 py-4 text-center">CTR</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-850/50">
                            @forelse($ads as $ad)
                                <tr class="hover:bg-slate-900/10 transition-colors">
                                    <td class="px-6 py-4 font-bold text-white">
                                        {{ $ad->name }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-slate-350">
                                        <span class="px-2 py-0.5 bg-slate-950 border border-slate-800 rounded text-[10px] uppercase font-bold text-indigo-400">
                                            {{ $adLocations[$ad->location] ?? $ad->location }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-400 font-mono capitalize">
                                        {{ $ad->type }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-slate-300 font-semibold">
                                        {{ number_format($ad->impressions_count) }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-slate-300 font-semibold">
                                        {{ number_format($ad->clicks_count) }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-slate-350 font-bold">
                                        {{ $ad->ctr }}%
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button wire:click="toggleAdStatus({{ $ad->id }})" 
                                            class="px-2 py-0.5 rounded text-[10px] font-bold uppercase transition-colors {{ $ad->is_active ? 'bg-emerald-950/20 text-emerald-400 border border-emerald-900/30' : 'bg-rose-950/20 text-rose-450 border border-rose-900/30' }}">
                                            {{ $ad->is_active ? 'Active' : 'Paused' }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-1">
                                        <button wire:click="editAd({{ $ad->id }})" class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">Edit</button>
                                        <button wire:click="deleteAd({{ $ad->id }})" onclick="confirm('Are you sure you want to delete this ad?') || event.stopImmediatePropagation()" class="text-rose-450 hover:text-rose-400 font-semibold transition-colors">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-slate-500">
                                        No active ad placements configured. Create one above to test.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-slate-950/20 border-t border-slate-800/40">
                    {{ $ads->links() }}
                </div>
            </div>
        @endif
    @endif

    <!-- --- AFFILIATE LINKS VIEW --- -->
    @if($activeTab === 'affiliates')
        @if($isCreatingAffiliate)
            <div class="bg-slate-900/40 backdrop-blur-md border border-slate-900 p-6 rounded-2xl space-y-4">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                    {{ $editingAffiliateId ? 'Edit Affiliate Link' : 'Add New Affiliate Link' }}
                </h3>
                <form wire:submit.prevent="saveAffiliate" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Match Keyword (Auto-Linking)</label>
                            <input wire:model="affiliateKeyword" type="text" class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Hostinger, Amazon Book" required>
                            <p class="text-[10px] text-slate-500 mt-1">This specific keyword inside post contents will automatically be turned into a link.</p>
                            @error('affiliateKeyword') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Redirect Slug (Local Redirect)</label>
                            <input wire:model="affiliateSlug" type="text" class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. hostinger-deal, book-recommendation" required>
                            <p class="text-[10px] text-slate-500 mt-1">Local URL path that cloaks the link: <code>/go/your-slug</code></p>
                            @error('affiliateSlug') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Destination Affiliate Target URL</label>
                        <input wire:model="affiliateTargetUrl" type="text" class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. https://hostinger.com/promo?id=your_affiliate_id" required>
                        @error('affiliateTargetUrl') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex space-x-3 pt-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-semibold text-white transition-colors">
                            Save Affiliate Link
                        </button>
                        <button type="button" wire:click="selectTab('affiliates')" class="px-4 py-2 bg-slate-950 border border-slate-850 text-slate-400 hover:text-white rounded-xl text-xs font-semibold transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- Affiliate links listing -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-850 text-slate-400 font-semibold uppercase tracking-wider bg-slate-950/40">
                                <th class="px-6 py-4">Keyword Mapping</th>
                                <th class="px-6 py-4">Cloaked URL Path</th>
                                <th class="px-6 py-4">Destination Target URL</th>
                                <th class="px-6 py-4 text-center">Redirect Clicks</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-850/50">
                            @forelse($affiliates as $aff)
                                <tr class="hover:bg-slate-900/10 transition-colors">
                                    <td class="px-6 py-4 font-bold text-white">
                                        {{ $aff->keyword }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-300 font-mono">
                                        <a href="/go/{{ $aff->slug }}" target="_blank" class="hover:underline text-indigo-400">
                                            /go/{{ $aff->slug }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-slate-400 max-w-xs truncate" title="{{ $aff->target_url }}">
                                        {{ $aff->target_url }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-white font-semibold">
                                        {{ number_format($aff->clicks_count) }}
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-1">
                                        <button wire:click="editAffiliate({{ $aff->id }})" class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">Edit</button>
                                        <button wire:click="deleteAffiliate({{ $aff->id }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" class="text-rose-450 hover:text-rose-400 font-semibold transition-colors">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                        No affiliate cloaking mappings active yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-slate-950/20 border-t border-slate-800/40">
                    {{ $affiliates->links() }}
                </div>
            </div>
        @endif
    @endif
</div>
