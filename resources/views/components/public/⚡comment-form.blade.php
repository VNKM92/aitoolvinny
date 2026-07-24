<?php

use Livewire\Component;
use App\Models\Comment;
use App\Models\Post;

new class extends Component
{
    public int $postId;
    
    // Form Inputs
    public string $authorName = '';
    public string $authorEmail = '';
    public string $content = '';
    public ?int $parentId = null;

    // Spam Protection
    public string $website = ''; // Honeypot
    public int $num1;
    public int $num2;
    public string $captchaAnswer = '';

    protected array $rules = [
        'authorName' => 'required_without:userId|nullable|string|max:100',
        'authorEmail' => 'required_without:userId|nullable|email|max:150',
        'content' => 'required|string|max:1000',
        'parentId' => 'nullable|exists:comments,id',
        'captchaAnswer' => 'required|integer',
    ];

    public function mount(int $postId)
    {
        $this->postId = $postId;
        if (auth()->check()) {
            $this->authorName = auth()->user()->name;
            $this->authorEmail = auth()->user()->email;
        }
        $this->generateCaptcha();
    }

    private function generateCaptcha()
    {
        $this->num1 = rand(1, 9);
        $this->num2 = rand(1, 9);
        $this->captchaAnswer = '';
    }

    public function setReply(int $id)
    {
        $this->parentId = $id;
    }

    public function cancelReply()
    {
        $this->parentId = null;
    }

    public function submitComment()
    {
        // 1. Honeypot check (Spam Protection)
        if (!empty($this->website)) {
            // Silently ignore spam submission
            $this->content = '';
            return;
        }

        // 2. Math Captcha check
        if ((int)$this->captchaAnswer !== ($this->num1 + $this->num2)) {
            $this->addError('captchaAnswer', 'Incorrect captcha answer. Please try again.');
            return;
        }

        $this->validate();

        Comment::create([
            'post_id' => $this->postId,
            'parent_id' => $this->parentId,
            'user_id' => auth()->id(),
            'author_name' => $this->authorName,
            'author_email' => $this->authorEmail,
            'content' => $this->content,
            'status' => 'pending', // Requires approval
        ]);

        $this->content = '';
        $this->parentId = null;
        $this->generateCaptcha();
        session()->flash('comment_message', 'Your comment has been submitted and is awaiting moderation.');
    }
};
?>

<div class="space-y-8 mt-12 pt-8 border-t border-slate-900">
    <h3 class="text-lg font-bold text-white">Comments Feed</h3>

    @if(session()->has('comment_message'))
        <div class="p-4 bg-emerald-950/20 border border-emerald-900/30 text-emerald-400 rounded-xl text-xs font-semibold">
            {{ session('comment_message') }}
        </div>
    @endif

    <!-- Comments Tree Rendering -->
    <div class="space-y-6">
        @php
            $comments = Comment::where('post_id', $postId)
                ->whereNull('parent_id')
                ->approved()
                ->with('replies')
                ->orderBy('created_at', 'asc')
                ->get();
        @endphp

        @forelse($comments as $comment)
            <div class="bg-slate-900/35 border border-slate-900 p-5 rounded-2xl space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-200 text-xs">{{ $comment->author_name }}</span>
                        <span class="text-[10px] text-slate-500 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <button wire:click="setReply({{ $comment->id }})" class="text-[10px] font-bold text-indigo-400 hover:text-indigo-300 transition-colors">
                        Reply
                    </button>
                </div>
                <p class="text-xs text-slate-300 whitespace-pre-wrap leading-relaxed">{{ $comment->content }}</p>

                <!-- Replies Level 2 -->
                @if($comment->replies->count() > 0)
                    <div class="pl-6 border-l border-slate-800 space-y-4 mt-4">
                        @foreach($comment->replies as $reply)
                            <div class="bg-slate-950/40 p-4 rounded-xl border border-slate-900/50 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="font-bold text-slate-200 text-xs">{{ $reply->author_name }}</span>
                                        <span class="text-[10px] text-slate-500 ml-2">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-300 whitespace-pre-wrap leading-relaxed">{{ $reply->content }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <p class="text-xs text-slate-500 text-center py-6">No discussions started. Be the first to share your thoughts!</p>
        @endforelse
    </div>

    <!-- Submit Form -->
    <div class="backdrop-blur-md bg-slate-900/40 border border-slate-900 p-6 rounded-2xl space-y-4">
        <h4 class="text-sm font-bold text-white">
            @if($parentId)
                Leave a Reply to Comment #{{ $parentId }}
            @else
                Join the Discussion
            @endif
        </h4>

        <form wire:submit.prevent="submitComment" class="space-y-4">
            @if($parentId)
                <div class="flex items-center justify-between bg-indigo-950/20 border border-indigo-900/30 px-3 py-2 rounded-lg text-xs text-indigo-400">
                    <span>Replying to nested comment thread...</span>
                    <button type="button" wire:click="cancelReply" class="font-bold hover:underline">Cancel</button>
                </div>
            @endif

            <!-- Honeypot Spam Protection Field (invisible to humans) -->
            <div style="display: none;">
                <label>Do not fill this field if you are human</label>
                <input wire:model="website" type="text">
            </div>

            @guest
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-450">Name</label>
                        <input wire:model="authorName" type="text" 
                            class="mt-1 block w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                            placeholder="John Doe" required>
                        @error('authorName') <span class="text-[10px] text-rose-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-450">Email</label>
                        <input wire:model="authorEmail" type="email" 
                            class="mt-1 block w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                            placeholder="john@example.com" required>
                        @error('authorEmail') <span class="text-[10px] text-rose-500 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            @endguest

            <div>
                <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-450">Your Comment</label>
                <textarea wire:model="content" rows="4" 
                    class="mt-1 block w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                    placeholder="Share your opinions on this post..." required></textarea>
                @error('content') <span class="text-[10px] text-rose-500 block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Math Captcha Spam Protection Field -->
            <div class="max-w-xs">
                <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-450">Spam Verification: {{ $num1 }} + {{ $num2 }} = ?</label>
                <input wire:model="captchaAnswer" type="text" required
                    class="mt-1 block w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                    placeholder="Enter the sum">
                @error('captchaAnswer') <span class="text-[10px] text-rose-500 block mt-1">{{ $message }}</span> @enderror
            </div>

            <button type="submit" 
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-xs font-semibold text-white transition-colors shadow-lg shadow-indigo-600/10">
                Submit Comment
            </button>
        </form>
    </div>
</div>