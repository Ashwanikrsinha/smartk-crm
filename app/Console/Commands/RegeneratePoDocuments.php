<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\PoDocumentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RegeneratePoDocuments extends Command
{
    /**
     * The name and signature of the Artisan command.
     */
    protected $signature = 'po:regenerate-docs
                            {--id= : Regenerate document for a specific invoice ID}
                            {--po= : Regenerate document for a specific PO number}
                            {--all : Regenerate documents for ALL approved POs}
                            {--missing : Only generate documents for approved POs that lack a valid document in storage}
                            {--status=approved : Target invoice status (default: approved)}';

    /**
     * The console command description.
     */
    protected $description = 'Regenerate PO DOCX documents in storage with updated PO Date and Point 6 (Order Type).';

    public function handle(PoDocumentService $documentService): int
    {
        $status      = $this->option('status') ?: Invoice::STATUS_APPROVED;
        $id          = $this->option('id');
        $po          = $this->option('po');
        $missingOnly = $this->option('missing');

        $query = Invoice::query();

        if ($id) {
            $query->where('id', $id);
        } elseif ($po) {
            $query->where('po_number', $po);
        } else {
            $query->where('status', $status);
        }

        $invoices = $query->orderBy('id')->get();
        $total    = $invoices->count();

        if ($total === 0) {
            $this->warn('No matching purchase orders found to process.');
            return 0;
        }

        $this->info("Found {$total} purchase orders to process.");

        $success = 0;
        $failed  = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($invoices as $invoice) {
            if ($missingOnly && $documentService->exists($invoice)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            try {
                $documentService->generate($invoice);
                $success++;
            } catch (\Throwable $e) {
                $failed++;
                Log::error("Failed to generate PO document via CLI for invoice #{$invoice->id}: " . $e->getMessage());
                $this->newLine();
                $this->error("Failed for PO {$invoice->po_number} (ID #{$invoice->id}): " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Completed PO document regeneration:");
        $this->line("  ✓ Successfully generated: {$success}");
        if ($skipped > 0) {
            $this->line("  ↷ Skipped (already exists): {$skipped}");
        }
        if ($failed > 0) {
            $this->error("  ✗ Failed: {$failed}");
        }

        return $failed > 0 ? 1 : 0;
    }
}
