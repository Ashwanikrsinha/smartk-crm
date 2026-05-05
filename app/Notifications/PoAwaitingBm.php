<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PoAwaitingBm extends Notification
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
            'title'   => "PO {$this->invoice->po_number} awaiting your approval",
            'message' => "SM has approved PO for {$this->invoice->customer->name}. "
                . "₹" . number_format($this->invoice->amount, 2) . " — please review.",
            'url'     => route('invoices.show', $this->invoice->id),
            'type'    => 'po_awaiting_bm',
        ];
    }
}
