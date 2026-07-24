<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-white">System Audit Logs</h1>
        <p class="text-slate-400 mt-1">Review user activities, updates, and configuration revisions.</p>
    </div>

    <!-- Logs Table -->
    <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 font-semibold uppercase tracking-wider bg-slate-950/40">
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">IP Address / User Agent</th>
                        <th class="px-6 py-4">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-900/20 transition-colors">
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
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
