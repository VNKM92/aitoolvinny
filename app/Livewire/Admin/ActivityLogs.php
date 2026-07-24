<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLogs extends Component
{
    use WithPagination;

    public array $selectedLogs = [];
    public bool $selectAll = false;

    /**
     * Delete a single activity log.
     */
    public function deleteLog(int $id)
    {
        $log = ActivityLog::findOrFail($id);
        $log->delete();
        
        $this->selectedLogs = array_diff($this->selectedLogs, [$id]);
        session()->flash('message', 'Activity log entry deleted.');
    }

    /**
     * Toggle select all logs on the current page.
     */
    public function updatedSelectAll($value)
    {
        if ($value) {
            $logsIds = ActivityLog::orderBy('id', 'desc')->take(20)->pluck('id')->toArray();
            $this->selectedLogs = $logsIds;
        } else {
            $this->selectedLogs = [];
        }
    }

    /**
     * Delete all selected logs.
     */
    public function deleteSelected()
    {
        if (empty($this->selectedLogs)) {
            return;
        }

        ActivityLog::whereIn('id', $this->selectedLogs)->delete();
        $this->selectedLogs = [];
        $this->selectAll = false;

        session()->flash('message', 'Selected activity logs deleted successfully.');
    }

    public function render()
    {
        // Activity logs are automatically scoped to the active tenant via TenantScope
        $logs = ActivityLog::with('user')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('livewire.admin.activity-logs', compact('logs'))
            ->layout('components.layouts.admin');
    }
}
