<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Services\PoDocumentService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PoApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  string  $recipientType  'school' | 'sp' | 'accounts'
     */
    public function __construct(
        public Invoice $invoice,
        public string  $recipientType = 'school'
    ) {}

    public function build(): static
    {
        $invoice = $this->invoice->fresh(); // fresh() to get po_document_path
        $service = app(PoDocumentService::class);

        $mail = $this
            ->subject("Purchase Order Approved — {$invoice->po_number}")
            ->view('emails.po-approved');

        // Attach from permanent storage (already generated before queuing)
        $fullPath = $service->storagePath($invoice);

        if ($fullPath) {
            $mail->attach($fullPath, [
                'as'   => "Purchase_Order_{$invoice->po_number}.docx",
                'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]);
        } else {
            \Log::warning('PO document not found in storage for mail', [
                'invoice_id'      => $invoice->id,
                'po_number'       => $invoice->po_number,
                'recipient_type'  => $this->recipientType,
                'document_path'   => $invoice->po_document_path,
            ]);
        }

        return $mail;
    }

    /**
     * Called by Laravel after the job completes successfully.
     * Records the timestamp so the UI can show mail delivery status.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('PoApprovedMail failed to send', [
            'invoice_id'     => $this->invoice->id,
            'recipient_type' => $this->recipientType,
            'error'          => $exception->getMessage(),
        ]);
        // Do NOT update the sent_at timestamp — null means failed/unsent
    }
}
