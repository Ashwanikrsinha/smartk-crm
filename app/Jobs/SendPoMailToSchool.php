<?php

namespace App\Jobs;

use App\Mail\PoApprovedMail;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

// ══════════════════════════════════════════════════════════
// SendPoMailToSchool
// ══════════════════════════════════════════════════════════

class SendPoMailToSchool implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public int $tries   = 3;
    public int $backoff = 60; // seconds between retries

    public function __construct(public Invoice $invoice) {}

    public function handle(): void
    {
        $email = $this->invoice->customer->email;

        if (!$email) {
            Log::info('No school email — PO mail skipped', ['po' => $this->invoice->po_number]);
            return;
        }

        Mail::to($email)->send(new PoApprovedMail($this->invoice, 'school'));

        // ✅ Mark as sent only after successful delivery
        $this->invoice->updateQuietly(['school_mail_sent_at' => now()]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('SendPoMailToSchool failed after all retries', [
            'invoice_id' => $this->invoice->id,
            'error'      => $e->getMessage(),
        ]);
        // school_mail_sent_at stays null → UI shows "Not Sent" with resend button
    }
}
