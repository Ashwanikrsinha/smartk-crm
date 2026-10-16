<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\SchoolOpeningBalance;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class PurchaseOrdersExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithTitle,
    WithEvents,
    ShouldAutoSize
{
    protected array $filters;
    protected User  $user;
    protected bool  $isMarketing;

    protected int $invoiceColCount = 0;
    protected int $totalColCount = 0;
    protected array $mergeRanges = [];
    protected array $obRowIndices = [];

    public function __construct(array $filters, User $user)
    {
        $this->filters     = $filters;
        $this->user        = $user;
        $this->isMarketing = $user->isMarketing();
    }

    public function title(): string
    {
        return 'Purchase Orders';
    }

    public function headings(): array
    {
        $base = [
            'PO Number',
            'PO Date',
            'Co-branding',
            'Order Type',
            'Sales Person',
            'Sales Manager',
            'School Name',
            'School Code',
        ];

        if ($this->isMarketing) {
            $base[] = 'School Email';
            $base[] = 'School Phone';
        }

        $base[] = 'State';
        $base[] = 'City';
        $base[] = 'Lead Source';
        $base[] = 'Status';
        $base[] = 'PO Amount (₹)';

        if (!$this->isMarketing) {
            $base[] = 'Billed Amount (₹)';
            $base[] = 'GST Amount (₹)';
            $base[] = 'Net Billed (₹)';
            $base[] = 'Pending PO (₹)';
            $base[] = 'Collection (₹)';
            $base[] = 'Outstanding (₹)';
            $base[] = 'Delivery Due Date';
            $base[] = 'Bilty Number';
            $base[] = 'POD Slip';
            $this->invoiceColCount = count($base); // 21 invoice-level columns

            $base[] = 'Product Type';
            $base[] = 'Product Name';
            $base[] = 'Ordered Qty';
            $base[] = 'Dispatched Qty';
            $base[] = 'Unit';
            $base[] = 'Rate (₹)';
            $base[] = 'Discount (%)';
            $base[] = 'Discount Amount (₹)';
            $base[] = 'Net Rate (₹)';
            $base[] = 'Item Amount (₹)';
        } else {
            $this->invoiceColCount = count($base);
        }

        $this->totalColCount = count($base);

        return $base;
    }

    public function collection(): Collection
    {
        if ($this->totalColCount === 0) {
            $this->headings();
        }

        $teamIds = $this->user->teamMemberIds();
        $f       = $this->filters;

        // ── 1. Fetch Invoices ───────────────────────────────────────────────
        $query = Invoice::with([
            'user:id,username,reportive_id,emp_code',
            'user.reportiveTo:id,username,emp_code',
            'customer:id,name,school_code,state,city,lead_source_id,email,phone_number',
            'customer.leadSource:id,name',
            'invoiceItems.product:id,name,category_id,price',
            'invoiceItems.product.category:id,name',
            'invoiceItems.unit:id,name',
            'dispatches.items:dispatch_id,invoice_item_id,quantity_dispatched',
        ])->whereIn('user_id', $teamIds);

        if (!empty($f['sp_id']))     $query->where('user_id', $f['sp_id']);
        if (!empty($f['school_id'])) $query->where('customer_id', $f['school_id']);
        if (!empty($f['status']))    $query->where('status', $f['status']);

        if (!empty($f['lead_source_id'])) {
            $query->whereHas('customer', fn($q) => $q->where('lead_source_id', $f['lead_source_id']));
        }
        if (!empty($f['state'])) {
            $query->whereHas('customer', fn($q) => $q->where('state', $f['state']));
        }
        if (!empty($f['month'])) {
            $query->whereYear('invoice_date',  substr($f['month'], 0, 4))
                ->whereMonth('invoice_date', substr($f['month'], 5, 2));
        } elseif (!empty($f['date_from']) && !empty($f['date_to'])) {
            $query->whereBetween('invoice_date', [$f['date_from'], $f['date_to']]);
        } else {
            $query->whereYear('invoice_date', $f['year'] ?? date('Y'));
        }

        $invoices = $query->orderByDesc('invoice_date')->get();

        // ── 2. Fetch Opening Balances for matching session ───────────────────
        $filterSessionYear = null;
        if (!empty($f['month'])) {
            $mYear  = (int) substr($f['month'], 0, 4);
            $mMonth = (int) substr($f['month'], 5, 2);
            $filterSessionYear = $mMonth >= 7 ? $mYear : $mYear - 1;
        } elseif (!empty($f['date_from']) && !empty($f['date_to'])) {
            $fromYear  = (int) substr($f['date_from'], 0, 4);
            $fromMonth = (int) substr($f['date_from'], 5, 2);
            $filterSessionYear = $fromMonth >= 7 ? $fromYear : $fromYear - 1;
        } else {
            $filterSessionYear = (int) ($f['session_year'] ?? $f['year'] ?? date('Y'));
        }

        $obQuery = SchoolOpeningBalance::with([
            'salesPerson:id,username,emp_code',
            'salesPerson.reportiveTo:id,username,emp_code',
            'customer:id,name,school_code,state,city,lead_source_id,email,phone_number',
            'customer.leadSource:id,name',
        ])
        ->forSession($filterSessionYear);

        $currentUser = $this->user ?? auth()->user();
        if ($currentUser && !$currentUser->isAdmin() && !$currentUser->isBusinessManager() && !$currentUser->isAccounts()) {
            $obQuery->where(function($q) use ($teamIds) {
                $q->whereIn('user_id', $teamIds)->orWhereNull('user_id');
            });
        }
        if (!empty($f['sp_id']))          $obQuery->where('user_id', $f['sp_id']);
        if (!empty($f['school_id']))      $obQuery->where('customer_id', $f['school_id']);
        if (!empty($f['lead_source_id'])) $obQuery->whereHas('customer', fn($q) => $q->where('lead_source_id', $f['lead_source_id']));
        if (!empty($f['state']))          $obQuery->whereHas('customer', fn($q) => $q->where('state', $f['state']));

        $openingBalances = $obQuery->get();

        // ── 3. Build Export Rows ─────────────────────────────────────────────
        $rows = collect();
        $currentRow = 2; // Data starts at Row 2

        // A. Prepend Opening Balance rows
        foreach ($openingBalances as $ob) {
            $obCols = [
                'Opening Balance (Carry-Forward)',
                'Session ' . $ob->sessionLabel(),
                '',
                '',
                $ob->salesPerson ? $ob->salesPerson->username . ' (' . ($ob->salesPerson->emp_code ?? '-') . ')' : '—',
                $ob->salesPerson?->reportiveTo?->username ?? '—',
                $ob->customer->name ?? '—',
                $ob->customer->school_code ?? '—',
            ];

            if ($this->isMarketing) {
                $obCols[] = $ob->customer->email        ?? '—';
                $obCols[] = $ob->customer->phone_number ?? '—';
            }

            $obCols[] = $ob->customer->state ?? '—';
            $obCols[] = $ob->customer->city  ?? '—';
            $obCols[] = $ob->customer->leadSource?->name ?? 'N/A';
            $obCols[] = 'Carry-Forward';
            $obCols[] = '0.00';

            if (!$this->isMarketing) {
                $obCols[] = '0.00';
                $obCols[] = '0.00';
                $obCols[] = '0.00';
                $obCols[] = '0.00';
                $obCols[] = number_format($ob->opening_collected, 2);
                $obCols[] = number_format($ob->opening_outstanding, 2);
                $obCols[] = '—'; // Delivery date
                $obCols[] = '—'; // Bilty
                $obCols[] = '—'; // POD

                // 10 item columns blank
                $itemCols = array_fill(0, 10, '—');
                $obCols = array_merge($obCols, $itemCols);
            }

            $rows->push($obCols);
            $this->obRowIndices[] = $currentRow;
            $currentRow++;
        }

        // B. Regular Invoice rows
        foreach ($invoices as $invoice) {
            $itemRowCount = max($invoice->invoiceItems->count(), 1);

            if (!$this->isMarketing) {
                $startRow = $currentRow;
                $endRow   = $startRow + $itemRowCount - 1;
                $this->mergeRanges[] = [$startRow, $endRow];
                $currentRow = $endRow + 1;
            } else {
                $currentRow++;
            }

            $mappedRows = $this->map($invoice);
            foreach ($mappedRows as $mRow) {
                $rows->push($mRow);
            }
        }

        return $rows;
    }

    public function map($invoice): array
    {
        if ($this->totalColCount === 0) {
            $this->headings();
        }

        $invoiceCols = [
            $invoice->po_number,
            $invoice->invoice_date?->format('d/m/Y'),
            $invoice->co_branding ? 'Yes' : 'No',
            $invoice->order_type ?? 'New On-board',
            $invoice->user ? $invoice->user->username . ' (' . $invoice->user->emp_code . ')' : '—',
            $invoice->user?->reportiveTo?->username ?? '—',
            $invoice->customer->name ?? '—',
            $invoice->customer->school_code ?? '—',
        ];

        if ($this->isMarketing) {
            $invoiceCols[] = $invoice->customer->email        ?? '—';
            $invoiceCols[] = $invoice->customer->phone_number ?? '—';
        }

        $invoiceCols[] = $invoice->customer->state ?? '—';
        $invoiceCols[] = $invoice->customer->city  ?? '—';
        $invoiceCols[] = $invoice->customer->leadSource?->name ?? 'N/A';
        $invoiceCols[] = ucfirst($invoice->status);
        $invoiceCols[] = number_format($invoice->amount, 2);

        if ($this->isMarketing) {
            return [$invoiceCols];
        }

        // Non-Marketing invoice financial cols
        $invoiceCols[] = number_format($invoice->billing_amount, 2);
        $gstAmt = (float) ($invoice->gst_amount ?? 0);
        $invoiceCols[] = number_format($gstAmt, 2);
        $invoiceCols[] = number_format(max(0, $invoice->billing_amount - $gstAmt), 2);
        $invoiceCols[] = number_format($invoice->amount - $invoice->billing_amount, 2);
        $invoiceCols[] = number_format($invoice->collected_amount, 2);
        $invoiceCols[] = number_format($invoice->outstanding_amount, 2);
        $invoiceCols[] = $invoice->delivery_due_date
            ? $invoice->delivery_due_date->format('d/m/Y')
            : '—';
        $invoiceCols[] = $invoice->dispatches->pluck('bilty_number')->filter()->implode(', ') ?: '—';
        $invoiceCols[] = $invoice->dispatches->whereNotNull('pod_slip')->isNotEmpty() ? 'Uploaded' : '—';

        $items = $invoice->invoiceItems;
        if ($items->isEmpty()) {
            return [array_merge($invoiceCols, array_fill(0, 10, '—'))];
        }

        $dispatchMap = [];
        foreach ($invoice->dispatches as $disp) {
            foreach ($disp->items as $di) {
                $iid = $di->invoice_item_id;
                if (!isset($dispatchMap[$iid])) $dispatchMap[$iid] = 0;
                $dispatchMap[$iid] += (float) $di->quantity_dispatched;
            }
        }

        $rows = [];
        foreach ($items as $i => $item) {
            $dispatched      = (float) ($dispatchMap[$item->id] ?? 0);
            $discountPercent = (float) ($item->discount ?? 0);
            $mrp             = (float) ($item->product?->price ?? 0);
            $savedRate       = (float) ($item->rate ?? 0);

            if ($mrp > 0) {
                $baseRate = $mrp;
            } elseif ($discountPercent > 0 && $savedRate > 0) {
                $baseRate = round($savedRate / (1 - ($discountPercent / 100)), 2);
            } else {
                $baseRate = $savedRate;
            }

            $discountAmount = $discountPercent > 0 ? round($baseRate * ($discountPercent / 100), 2) : 0.0;
            $netRate        = max(0, round($baseRate - $discountAmount, 2));

            $itemCols = [
                $item->product->category->name ?? '—',
                $item->product->name ?? $item->description ?? '—',
                $item->quantity,
                $dispatched,
                $item->unit->name ?? '—',
                number_format($baseRate, 2),
                number_format($discountPercent, 2),
                number_format($discountAmount, 2),
                number_format($netRate, 2),
                number_format($item->amount, 2),
            ];

            $rows[] = $i === 0
                ? array_merge($invoiceCols, $itemCols)
                : array_merge(array_fill(0, count($invoiceCols), ''), $itemCols);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = Coordinate::stringFromColumnIndex($this->totalColCount ?: 31);

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
        ]);

        // Style Opening Balance rows with distinct soft amber background
        foreach ($this->obRowIndices as $rowIdx) {
            $sheet->getStyle("A{$rowIdx}:{$lastCol}{$rowIdx}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3CD']],
                'font' => ['bold' => true],
            ]);
        }

        $sheet->freezePane('A2');

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                foreach ($this->mergeRanges as [$startRow, $endRow]) {
                    if ($endRow > $startRow) {
                        for ($col = 1; $col <= $this->invoiceColCount; $col++) {
                            $colLetter = Coordinate::stringFromColumnIndex($col);
                            $sheet->mergeCells("{$colLetter}{$startRow}:{$colLetter}{$endRow}");
                            $sheet->getStyle("{$colLetter}{$startRow}:{$colLetter}{$endRow}")
                                ->getAlignment()
                                ->setVertical(Alignment::VERTICAL_CENTER);
                        }
                    }

                    $lastCol = Coordinate::stringFromColumnIndex($this->totalColCount ?: 31);
                    $sheet->getStyle("A{$endRow}:{$lastCol}{$endRow}")
                        ->getBorders()->getBottom()
                        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }
            },
        ];
    }
}
