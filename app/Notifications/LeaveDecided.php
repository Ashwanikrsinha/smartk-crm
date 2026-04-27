<?php
namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LeaveDecided extends Notification
{
    use Queueable;

    public function __construct(protected Leave $leave) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $status = strtoupper($this->leave->status);
        return [
            'title'   => "Leave {$status}",
            'message' => "Your {$this->leave->leave_type} leave request has been {$this->leave->status}."
                . ($this->leave->manager_remarks ? " Remarks: {$this->leave->manager_remarks}" : ''),
            'url'     => route('leaves.show', $this->leave->id),
            'type'    => 'leave_decided',
        ];
    }
}
