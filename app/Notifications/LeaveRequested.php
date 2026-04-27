<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LeaveRequested extends Notification
{
    use Queueable;

    public function __construct(protected Leave $leave) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'   => "Leave Request from {$this->leave->user->username}",
            'message' => ucfirst($this->leave->leave_type) . " leave ({$this->leave->days} days): {$this->leave->reason}",
            'url'     => route('leaves.show', $this->leave->id),
            'type'    => 'leave_requested',
        ];
    }
}
