<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-white">Comments Moderation</h1>
        <p class="text-slate-400 mt-1">Approve, reject, or reply to reader feedback on your blog posts.</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <div class="space-y-6">
        <!-- Status filter top tab bar -->
        <div class="flex items-center justify-between border-b border-slate-900 pb-4">
            <div class="flex space-x-2">
                @foreach(['all' => 'All Comments', 'pending' => 'Pending Approval', 'approved' => 'Approved', 'spam' => 'Spam Queue'] as $status => $label)
                    <button wire:click="$set('statusFilter', '{{ $status }}')" 
                        class="px-4 py-2 rounded-lg text-xs font-semibold border transition-all cursor-pointer {{ $statusFilter === $status ? 'bg-indigo-600 border-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        @if($replyCommentId)
            <!-- Admin Reply drawer -->
            <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 p-6 rounded-2xl max-w-xl">
                <h3 class="text-md font-bold text-white mb-2">Write Reply to Comment</h3>
                
                <form wire:submit.prevent="saveReply" class="space-y-4">
                    <textarea wire:model="replyText" rows="4" 
                        class="block w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs placeholder-slate-655 focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                        placeholder="Compose your reply text here..." required></textarea>
                    @error('replyText') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror

                    <div class="flex space-x-2">
                        <button type="button" wire:click="$set('replyCommentId', null)" 
                            class="px-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-slate-400 text-xs transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-white text-xs font-semibold transition-colors">
                            Submit Reply
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Comments Moderation Table -->
        <div class="backdrop-blur-xl bg-slate-900/60 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase tracking-wider bg-slate-950/40">
                            <th class="px-6 py-4">Author</th>
                            <th class="px-6 py-4">Comment</th>
                            <th class="px-6 py-4">Article</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($comments as $comment)
                            <tr class="hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-white">{{ $comment->author_name }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $comment->author_email ?? 'System User' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs text-slate-300 max-w-sm whitespace-pre-wrap">{{ $comment->content }}</p>
                                    <div class="text-[10px] text-slate-500 mt-1">{{ $comment->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400">
                                    @if($comment->post)
                                        <a href="/posts/{{ $comment->post->slug }}" target="_blank" class="text-indigo-400 hover:underline">
                                            {{ $comment->post->title[app()->getLocale()] ?? reset($comment->post->title) }}
                                        </a>
                                    @else
                                        <span class="text-slate-500">Deleted Post</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $comment->status === 'approved' ? 'bg-emerald-950/20 text-emerald-400 border-emerald-900/55' : ($comment->status === 'spam' ? 'bg-rose-950/20 text-rose-400 border-rose-900/55' : 'bg-amber-950/20 text-amber-400 border-amber-900/55') }}">
                                        {{ ucfirst($comment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-3 text-sm font-medium">
                                    @if($comment->status !== 'approved')
                                        <button wire:click="approveComment({{ $comment->id }})" 
                                            class="text-emerald-400 hover:text-emerald-300 font-semibold transition-colors">
                                            Approve
                                        </button>
                                    @else
                                        <button wire:click="rejectComment({{ $comment->id }})" 
                                            class="text-amber-400 hover:text-amber-300 font-semibold transition-colors">
                                            Hold
                                        </button>
                                    @endif

                                    <button wire:click="setReply({{ $comment->id }})" 
                                        class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">
                                        Reply
                                    </button>

                                    @if($comment->status !== 'spam')
                                        <button wire:click="spamComment({{ $comment->id }})" 
                                            class="text-rose-500 hover:text-rose-455 font-semibold transition-colors">
                                            Spam
                                        </button>
                                    @endif

                                    <button onclick="confirm('Delete this comment permanently?') || event.stopImmediatePropagation()"
                                        wire:click="deleteComment({{ $comment->id }})" 
                                        class="text-slate-500 hover:text-white font-semibold transition-colors">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                    No comments found in this queue.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-slate-950/20 border-t border-slate-800/40">
                {{ $comments->links() }}
            </div>
        </div>
    </div>
</div>
