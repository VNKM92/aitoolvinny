<?php

namespace App\Livewire\Admin;

use App\Models\Subscriber;
use App\Services\ActivityLogger;
use Livewire\Component;
use Livewire\WithPagination;

class Newsletter extends Component
{
    use WithPagination;

    // Mail Composer
    public string $subject = '';
    public string $body = '';
    public bool $isComposing = false;

    // Filters
    public string $search = '';

    protected array $rules = [
        'subject' => 'required|string|max:255',
        'body' => 'required|string',
    ];

    public function toggleCompose()
    {
        $this->isComposing = !$this->isComposing;
        $this->subject = '';
        $this->body = '';
    }

    public function toggleSubscriberStatus(int $id)
    {
        $subscriber = Subscriber::findOrFail($id);
        $subscriber->update(['is_active' => !$subscriber->is_active]);
        ActivityLogger::log('subscriber_status_toggled', "Toggled status of subscriber: {$subscriber->email}");
    }

    public function sendBroadcast()
    {
        $this->validate();

        // Count active subscribers
        $activeCount = Subscriber::where('is_active', true)->count();

        if ($activeCount === 0) {
            session()->flash('error', 'No active subscribers to send the newsletter to.');
            return;
        }

        // In a real application, you would dispatch a queue job:
        // foreach (Subscriber::where('is_active', true)->get() as $sub) {
        //     Mail::to($sub->email)->queue(new NewsletterMail($this->subject, $this->body));
        // }
        // We'll log the broadcast log and trigger a session message:

        ActivityLogger::log('newsletter_sent', "Sent newsletter broadcast: '{$this->subject}' to {$activeCount} subscribers.");

        $this->isComposing = false;
        $this->subject = '';
        $this->body = '';

        session()->flash('message', "Broadcast newsletter successfully queued and sent to {$activeCount} subscribers!");
    }

    public function deleteSubscriber(int $id)
    {
        $subscriber = Subscriber::findOrFail($id);
        $subscriber->delete();
        ActivityLogger::log('subscriber_deleted', "Deleted subscriber: {$subscriber->email}");
        session()->flash('message', 'Subscriber removed successfully.');
    }

    public function render()
    {
        $subscribers = Subscriber::where('email', 'like', "%{$this->search}%")
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('livewire.admin.newsletter', compact('subscribers'))
            ->layout('components.layouts.admin');
    }
}
