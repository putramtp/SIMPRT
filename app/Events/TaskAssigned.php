<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task->load(['customer', 'assignee']);
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('App.Models.User.' . $this->task->assigned_to)];
    }

    public function broadcastAs(): string
    {
        return 'TaskAssigned';
    }

    public function broadcastWith(): array
    {
        return [
            'id'            => $this->task->id,
            'title'         => $this->task->title,
            'customer_name' => $this->task->customer?->name ?? '-',
            'due_date'      => $this->task->due_date?->format('d M Y') ?? '-',
            'url'           => route('tugas.show', $this->task),
        ];
    }
}
