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


class SendPoMailToSp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(public Invoice $invoice) {}

    public function handle(): void
    {
        $email = $this->invoice->user->email ?? null;

        if (!$email) {
            Log::info('No SP email — PO mail skipped', ['po' => $this->invoice->po_number]);
            return;
        }

        Mail::to($email)->send(new PoApprovedMail($this->invoice, 'sp'));

        $this->invoice->updateQuietly(['sp_mail_sent_at' => now()]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('SendPoMailToSp failed after all retries', [
            'invoice_id' => $this->invoice->id,
            'error'      => $e->getMessage(),
        ]);
    }
}
