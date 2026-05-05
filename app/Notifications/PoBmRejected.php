<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PoBmRejected extends Notification
{
    use Queueable;

    public function __construct(protected Invoice $invoice) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'   => "PO {$this->invoice->po_number} returned by BM",
            'message' => "Business Manager returned PO for {$this->invoice->customer->name}. "
                . "Reason: {$this->invoice->rejection_reason}",
            'url'     => route('invoices.show', $this->invoice->id),
            'type'    => 'po_bm_rejected',
        ];
    }
}
