<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLogs extends Component
{
    use WithPagination;

    public function render()
    {
        // Activity logs are automatically scoped to the active tenant via TenantScope!
        $logs = ActivityLog::with('user')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('livewire.admin.activity-logs', compact('logs'))
            ->layout('components.layouts.admin');
    }
}
