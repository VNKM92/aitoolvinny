<?php

namespace App\Livewire\Admin;

use App\Models\Comment;
use App\Services\ActivityLogger;
use Livewire\Component;
use Livewire\WithPagination;

class Comments extends Component
{
    use WithPagination;

    public ?int $replyCommentId = null;
    public string $replyText = '';

    // Filters
    public string $statusFilter = 'all';

    protected array $rules = [
        'replyText' => 'required|string|max:1000',
    ];

    public function approveComment(int $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->update(['status' => 'approved']);
        ActivityLogger::log('comment_approved', "Approved comment from: {$comment->author_name} on Post #{$comment->post_id}");
        session()->flash('message', 'Comment approved successfully.');
    }

    public function rejectComment(int $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->update(['status' => 'pending']);
        ActivityLogger::log('comment_rejected', "Rejected comment from: {$comment->author_name} to pending queue");
        session()->flash('message', 'Comment status reverted to pending.');
    }

    public function spamComment(int $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->update(['status' => 'spam']);
        ActivityLogger::log('comment_spammed', "Marked comment from: {$comment->author_name} as SPAM");
        session()->flash('message', 'Comment spammed.');
    }

    public function setReply(int $id)
    {
        $this->replyCommentId = $id;
        $this->replyText = '';
    }

    public function saveReply()
    {
        $this->validate();

        $parent = Comment::findOrFail($this->replyCommentId);

        Comment::create([
            'post_id' => $parent->post_id,
            'parent_id' => $parent->id,
            'user_id' => auth()->id(),
            'author_name' => auth()->user()->name,
            'author_email' => auth()->user()->email,
            'content' => $this->replyText,
            'status' => 'approved', // Admin replies are auto-approved
        ]);

        ActivityLogger::log('comment_replied', "Replied to comment #{$parent->id}");

        $this->replyCommentId = null;
        $this->replyText = '';
        session()->flash('message', 'Reply published successfully.');
    }

    public function deleteComment(int $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        ActivityLogger::log('comment_deleted', "Deleted comment from: {$comment->author_name}");
        session()->flash('message', 'Comment deleted successfully.');
    }

    public function render()
    {
        $query = Comment::with('post')->orderBy('id', 'desc');

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $comments = $query->paginate(15);

        return view('livewire.admin.comments', compact('comments'))
            ->layout('components.layouts.admin');
    }
}
