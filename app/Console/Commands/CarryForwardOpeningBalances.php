<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\SchoolOpeningBalance;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CarryForwardOpeningBalances extends Command
{
    /**
     * The name and signature of the Artisan command.
     */
    protected $signature = 'crm:carry-forward-balances
                            {--closing-year= : The session_year that is CLOSING (e.g. 2025 for 2025-26). Defaults to previous session year.}
                            {--dry-run : Print what would be created without saving}';

    protected $description = 'Auto-generate Opening Balance records for the new session based on unsettled POs from the closing session.';

    public function handle(): int
    {
        // Determine the closing session year
        $closingYear = (int) ($this->option('closing-year')
            ?? SchoolOpeningBalance::currentSessionYear() - 1);

        $newSessionYear = $closingYear + 1;

        $closingRange = SchoolOpeningBalance::sessionDateRange($closingYear);

        $isDryRun = $this->option('dry-run');

        $this->info("Carrying forward from session {$closingYear}-" . ($closingYear + 1) % 100
            . " → {$newSessionYear}-" . ($newSessionYear + 1) % 100);

        if ($isDryRun) {
            $this->warn('DRY RUN — no records will be saved.');
        }

        // ── Aggregate POs for each school in the closing session ─────────────
        $results = DB::table('invoices')
            ->whereBetween('invoice_date', [$closingRange['start'], $closingRange['end']])
            ->where('status', Invoice::STATUS_APPROVED)
            ->select(
                'customer_id',
                DB::raw('SUM(amount) as total_po'),
                DB::raw('SUM(billing_amount) as total_billed'),
                DB::raw('SUM(collected_amount) as total_collected')
            )
            ->groupBy('customer_id')
            ->having(DB::raw('SUM(amount) - SUM(collected_amount)'), '>', 0)
            ->get();

        $created  = 0;
        $skipped  = 0;

        foreach ($results as $row) {
            $outstanding = round((float) $row->total_po - (float) $row->total_collected, 2);

            if ($outstanding <= 0) {
                $skipped++;
                continue;
            }

            // Skip if an opening balance for the new session already exists for this school
            $exists = SchoolOpeningBalance::where('customer_id', $row->customer_id)
                ->where('session_year', $newSessionYear)
                ->exists();

            if ($exists) {
                $this->line("  SKIP (exists) customer_id={$row->customer_id}");
                $skipped++;
                continue;
            }

            // Determine the SP with the most POs for this school in the closing session
            $topSp = Invoice::whereBetween('invoice_date', [$closingRange['start'], $closingRange['end']])
                ->where('customer_id', $row->customer_id)
                ->where('status', Invoice::STATUS_APPROVED)
                ->select('user_id', DB::raw('COUNT(*) as cnt'))
                ->groupBy('user_id')
                ->orderByDesc('cnt')
                ->value('user_id');

            // System user (first admin) as entered_by for auto records
            $systemUser = User::whereHas('role', fn($q) => $q->where('name', 'Administrator'))
                ->orderBy('id')
                ->value('id') ?? 1;

            $this->line("  + customer_id={$row->customer_id} | outstanding=₹{$outstanding} | sp_id={$topSp}");

            if (!$isDryRun) {
                try {
                    DB::transaction(function () use ($row, $outstanding, $topSp, $systemUser, $newSessionYear, $closingYear) {
                        SchoolOpeningBalance::create([
                            'customer_id'         => $row->customer_id,
                            'session_year'        => $newSessionYear,
                            'user_id'             => $topSp,
                            'opening_amount'      => $outstanding,
                            'opening_billed'      => round((float) $row->total_billed, 2),
                            'opening_collected'   => 0,
                            'opening_outstanding' => $outstanding,
                            'source'              => 'auto',
                            'notes'               => "Auto carry-forward from session {$closingYear}-" . ($closingYear + 1) % 100,
                            'entered_by'          => $systemUser,
                        ]);
                    });
                    $created++;
                } catch (\Throwable $e) {
                    $this->error("  ERROR customer_id={$row->customer_id}: " . $e->getMessage());
                    Log::error('CarryForwardOpeningBalances error', [
                        'customer_id' => $row->customer_id,
                        'error'       => $e->getMessage(),
                    ]);
                }
            } else {
                $created++;
            }
        }

        $this->info("Done. Created: {$created} | Skipped: {$skipped}");

        return self::SUCCESS;
    }
}
