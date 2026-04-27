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

class SendPoMailToAccounts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(public Invoice $invoice) {}

    public function handle(): void
    {
        $accountsUsers = User::where('role_id', function ($q) {
            $q->select('id')->from('roles')->where('name', 'Accounts');
        })
            ->whereNotNull('email')
            ->active()
            ->get();

        if ($accountsUsers->isEmpty()) {
            Log::info('No Accounts users found — PO mail skipped', ['po' => $this->invoice->po_number]);
            return;
        }

        foreach ($accountsUsers as $user) {
            Mail::to($user->email)->send(new PoApprovedMail($this->invoice, 'accounts'));
        }

        $this->invoice->updateQuietly(['accounts_mail_sent_at' => now()]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('SendPoMailToAccounts failed after all retries', [
            'invoice_id' => $this->invoice->id,
            'error'      => $e->getMessage(),
        ]);
    }
}
