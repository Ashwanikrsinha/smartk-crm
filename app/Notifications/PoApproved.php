<?php
namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class PoApproved extends Notification
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
            'title'   => "PO {$this->invoice->po_number} Approved",
            'message' => "Your Purchase Order for {$this->invoice->customer->name} has been approved.",
            'url'     => route('invoices.show', $this->invoice->id),
            'type'    => 'po_approved',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("PO Approved: {$this->invoice->po_number}")
            ->greeting("Hello {$notifiable->username}!")
            ->line("Your Purchase Order **{$this->invoice->po_number}** for **{$this->invoice->customer->name}** has been approved.")
            ->line("Amount: ₹" . number_format($this->invoice->amount, 2))
            ->action('View PO', route('invoices.show', $this->invoice->id))
            ->line('A copy has been sent to the school and Accounts team.');
    }
}
