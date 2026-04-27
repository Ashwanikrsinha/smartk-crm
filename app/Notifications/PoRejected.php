<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PoRejected extends Notification
{
    use Queueable;

    public function __construct(protected Invoice $invoice) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'   => "PO {$this->invoice->po_number} Rejected",
            'message' => "Your PO for {$this->invoice->customer->name} needs correction: {$this->invoice->rejection_reason}",
            'url'     => route('invoices.edit', $this->invoice->id),
            'type'    => 'po_rejected',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("PO Rejected: {$this->invoice->po_number}")
            ->greeting("Hello {$notifiable->username}!")
            ->line("Your Purchase Order **{$this->invoice->po_number}** has been returned for correction.")
            ->line("**Reason:** {$this->invoice->rejection_reason}")
            ->action('Edit and Resubmit', route('invoices.edit', $this->invoice->id));
    }
}
