<div>
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white">System Audit Logs</h1>
            <p class="text-slate-400 mt-1">Review user activities, updates, and configuration revisions.</p>
        </div>
        
        @if(count($selectedLogs) > 0)
            <button wire:click="deleteSelected" onclick="confirm('Are you sure you want to delete the selected logs?') || event.stopImmediatePropagation()" 
                class="px-4 py-2 bg-rose-600 hover:bg-rose-500 rounded-xl text-xs font-semibold text-white transition-colors flex items-center shadow-lg shadow-rose-600/10">
                <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete Selected ({{ count($selectedLogs) }})
            </button>
        @endif
    </div>

    @if(session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-xl text-xs font-semibold">
            {{ session('message') }}
        </div>
    @endif

    <!-- Logs Table -->
    <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 font-semibold uppercase tracking-wider bg-slate-950/40">
                        <th class="px-6 py-4 w-12">
                            <input wire:model.live="selectAll" type="checkbox" class="rounded border-slate-800 bg-slate-950 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">IP Address / User Agent</th>
                        <th class="px-6 py-4">Timestamp</th>
                        <th class="px-6 py-4 w-12 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-900/20 transition-colors {{ in_array($log->id, $selectedLogs) ? 'bg-indigo-950/10' : '' }}">
                            <td class="px-6 py-4">
                                <input wire:model.live="selectedLogs" type="checkbox" value="{{ $log->id }}" class="rounded border-slate-800 bg-slate-950 text-indigo-600 focus:ring-indigo-500">
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-950 border border-indigo-950 text-indigo-400 uppercase tracking-wide">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-white">
                                {{ $log->user->name ?? 'System Guest' }}
                            </td>
                            <td class="px-6 py-4 text-slate-300 max-w-sm">
                                {{ $log->description }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                <div>IP: <code>{{ $log->ip_address }}</code></div>
                                <div class="truncate max-w-xs mt-0.5" title="{{ $log->user_agent }}">{{ $log->user_agent }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-400 font-medium">
                                {{ $log->created_at->format('M d, Y @ H:i:s') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="deleteLog({{ $log->id }})" onclick="confirm('Are you sure you want to delete this log entry?') || event.stopImmediatePropagation()" 
                                    class="text-slate-500 hover:text-rose-450 p-1 transition-colors">
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                No activity log records compiled yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-slate-950/20 border-t border-slate-800/40">
            {{ $logs->links() }}
        </div>
    </div>
</div>
