<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Notifications\Notification;

class TaskStartedNotification extends Notification
{
    public function __construct(public Task $task) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'          => 'task_started',
            'task_id'       => $this->task->id,
            'title'         => $this->task->title,
            'teknisi_name'  => $this->task->assignees->pluck('name')->join(', ') ?: '-',
            'customer_name' => $this->task->customer?->name ?? '-',
            'url'           => route('tugas.show', $this->task),
        ];
    }
}
