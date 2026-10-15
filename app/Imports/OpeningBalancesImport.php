<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\SchoolOpeningBalance;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OpeningBalancesImport implements ToCollection, WithHeadingRow
{
    protected int   $importedCount = 0;
    protected int   $skippedCount  = 0;
    protected array $errors        = [];

    /** Session year this import applies to (default = current session) */
    protected int $sessionYear;

    public function __construct(?int $sessionYear = null)
    {
        $this->sessionYear = $sessionYear ?? SchoolOpeningBalance::currentSessionYear();
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            // ── Resolve school ─────────────────────────────────────────────
            $schoolCode = trim((string) ($row['school_code'] ?? ''));
            $schoolName = trim((string) ($row['school_name'] ?? ''));

            if (empty($schoolCode) && empty($schoolName)) {
                $this->skippedCount++;
                continue;
            }

            $customer = null;
            if ($schoolCode) {
                $customer = Customer::where('school_code', $schoolCode)->first();
            }
            if (!$customer && $schoolName) {
                $customer = Customer::where('name', 'like', "%{$schoolName}%")->first();
            }

            if (!$customer) {
                $this->errors[] = "Row {$rowNum}: School not found (code='{$schoolCode}', name='{$schoolName}'). Skipped.";
                $this->skippedCount++;
                continue;
            }

            // ── Resolve amounts ────────────────────────────────────────────
            $rawAmount     = $row['opening_amount'] ?? $row['carry_forward_amount'] ?? null;
            $openingAmount = $this->parseAmount($rawAmount);
            $alreadyPaid   = $this->parseAmount($row['already_collected'] ?? $row['already_paid'] ?? 0) ?? 0.0;

            if ($openingAmount === null || $openingAmount == 0.0) {
                $this->errors[] = "Row {$rowNum}: Opening Amount is 0 or blank for '{$schoolCode}'. Skipped.";
                $this->skippedCount++;
                continue;
            }

            $outstanding = $openingAmount >= 0
                ? max(0, $openingAmount - $alreadyPaid)
                : ($openingAmount - $alreadyPaid);

            // ── Resolve SP (optional) ──────────────────────────────────────
            $spEmpCode = trim((string) ($row['sp_employee_code'] ?? $row['employee_code'] ?? ''));
            $spUser    = null;
            if ($spEmpCode) {
                $spUser = User::where('emp_code', $spEmpCode)->first();
            }

            $notes = trim((string) ($row['notes'] ?? $row['remarks'] ?? ''));

            // ── Upsert (skip if already exists for this school+session) ────
            $exists = SchoolOpeningBalance::where('customer_id', $customer->id)
                ->where('session_year', $this->sessionYear)
                ->exists();

            if ($exists) {
                $this->errors[] = "Row {$rowNum}: Opening balance already exists for '{$schoolCode}' in session {$this->sessionYear}. Skipped.";
                $this->skippedCount++;
                continue;
            }

            try {
                DB::transaction(function () use ($customer, $spUser, $openingAmount, $alreadyPaid, $outstanding, $notes) {
                    SchoolOpeningBalance::create([
                        'customer_id'         => $customer->id,
                        'session_year'        => $this->sessionYear,
                        'user_id'             => $spUser?->id,
                        'opening_amount'      => $openingAmount,
                        'opening_billed'      => $openingAmount,   // for manual: full amount = billed from prev session
                        'opening_collected'   => $alreadyPaid,
                        'opening_outstanding' => $outstanding,
                        'source'              => 'manual',
                        'notes'               => $notes ?: null,
                        'entered_by'          => auth()->id(),
                    ]);
                });
                $this->importedCount++;
            } catch (\Throwable $e) {
                $this->errors[] = "Row {$rowNum}: Error saving record for '{$schoolCode}': " . $e->getMessage();
                $this->skippedCount++;
                Log::error('OpeningBalancesImport error', [
                    'row'        => $rowNum,
                    'school'     => $schoolCode,
                    'error'      => $e->getMessage(),
                ]);
            }
        }
    }

    public function getImportedCount(): int { return $this->importedCount; }
    public function getSkippedCount(): int  { return $this->skippedCount;  }
    public function getErrors(): array      { return $this->errors;         }

    /**
     * Parse numeric amounts safely, preserving negative values.
     * Handles Excel-formatted strings like "-25,000.00", "(25000)", "-₹25000", etc.
     */
    private function parseAmount($val): ?float
    {
        if ($val === null || $val === '') {
            return null;
        }

        if (is_numeric($val)) {
            return (float) $val;
        }

        $str = trim((string) $val);

        // Account for parentheses notation for negative numbers e.g. (25,000)
        if (preg_match('/^\((.+)\)$/', $str, $matches)) {
            $str = '-' . $matches[1];
        }

        // Strip currency symbols, commas, and whitespace
        $cleaned = preg_replace('/[^\d.-]/', '', $str);

        if ($cleaned === '' || !is_numeric($cleaned)) {
            return null;
        }

        return (float) $cleaned;
    }
}
