<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PoFullyApproved extends Notification
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
            'title'   => "PO {$this->invoice->po_number} Fully Approved ✓",
            'message' => "Your PO for {$this->invoice->customer->name} has been fully approved "
                . "by the Business Manager. The school has been notified.",
            'url'     => route('invoices.show', $this->invoice->id),
            'type'    => 'po_fully_approved',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("PO Fully Approved — {$this->invoice->po_number}")
            ->greeting("Hello {$notifiable->username}!")
            ->line("Your Purchase Order **{$this->invoice->po_number}** has been fully approved.")
            ->line("School: {$this->invoice->customer->name}")
            ->line("Amount: ₹" . number_format($this->invoice->amount, 2))
            ->action('View PO', route('invoices.show', $this->invoice->id));
    }
}
