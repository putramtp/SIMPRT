<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    public function __construct(public Task $task) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'task_id'       => $this->task->id,
            'title'         => $this->task->title,
            'customer_name' => $this->task->customer?->name ?? '-',
            'due_date'      => $this->task->due_date?->format('d M Y') ?? '-',
            'url'           => route('tugas.show', $this->task),
        ];
    }
}
