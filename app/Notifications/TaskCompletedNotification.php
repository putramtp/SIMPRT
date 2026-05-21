<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Notifications\Notification;

class TaskCompletedNotification extends Notification
{
    public function __construct(public Report $report) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'          => 'task_completed',
            'task_id'       => $this->report->task_id,
            'title'         => $this->report->task?->title ?? '-',
            'teknisi_name'  => $this->report->teknisi?->name ?? '-',
            'customer_name' => $this->report->task?->customer?->name ?? '-',
            'url'           => route('laporan.show', $this->report),
        ];
    }
}
