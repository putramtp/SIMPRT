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
        $this->task = $task->load(['customer', 'assignees']);
    }

    public function broadcastOn(): array
    {
        return $this->task->assignees
            ->map(fn($u) => new PrivateChannel('App.Models.User.' . $u->id))
            ->all();
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
