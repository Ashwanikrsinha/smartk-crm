<?php

// namespace App\Exports;

// use App\Models\Invoice;
// use App\Models\User;
// use Maatwebsite\Excel\Concerns\FromQuery;
// use Maatwebsite\Excel\Concerns\WithHeadings;
// use Maatwebsite\Excel\Concerns\WithMapping;
// use Maatwebsite\Excel\Concerns\WithStyles;
// use Maatwebsite\Excel\Concerns\WithTitle;
// use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
// use PhpOffice\PhpSpreadsheet\Style\Fill;

// class PurchaseOrdersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
// {
//     protected array $filters;
//     protected User  $user;
//     protected bool  $isMarketing;

//     public function __construct(array $filters, User $user)
//     {
//         $this->filters     = $filters;
//         $this->user        = $user;
//         $this->isMarketing = $user->isMarketing();
//     }

//     public function title(): string
//     {
//         return 'Purchase Orders';
//     }

//     public function query()
//     {
//         $teamIds = $this->user->teamMemberIds();

//         $query = Invoice::with([
//             'user:id,username,reportive_id,emp_code',
//             'user.reportiveTo:id,username,emp_code',
//             'customer:id,name,school_code,state,city,lead_source_id,email,phone_number',
//             'customer.leadSource:id,name',
//         ])->whereIn('user_id', $teamIds);

//         $f = $this->filters;

//         if (!empty($f['sp_id']))     $query->where('user_id', $f['sp_id']);
//         if (!empty($f['school_id'])) $query->where('customer_id', $f['school_id']);
//         if (!empty($f['status']))    $query->where('status', $f['status']);

//         if (!empty($f['lead_source_id'])) {
//             $query->whereHas('customer', fn($q) => $q->where('lead_source_id', $f['lead_source_id']));
//         }
//         if (!empty($f['state'])) {
//             $query->whereHas('customer', fn($q) => $q->where('state', $f['state']));
//         }
//         if (!empty($f['month'])) {
//             $query->whereYear('invoice_date',  substr($f['month'], 0, 4))
//                 ->whereMonth('invoice_date', substr($f['month'], 5, 2));
//         } elseif (!empty($f['date_from']) && !empty($f['date_to'])) {
//             $query->whereBetween('invoice_date', [$f['date_from'], $f['date_to']]);
//         } else {
//             $query->whereYear('invoice_date', $f['year'] ?? date('Y'));
//         }

//         return $query->orderByDesc('invoice_date');
//     }

//     // -------------------------------------------------------
//     // Column order:
//     //
//     // PO Number | PO Date | SP | SM | School Name | School Code
//     //   → [Marketing only: Email | Phone]
//     // State | City | Lead Source | Status | PO Amount
//     //   → [Non-Marketing: Billed | Pending PO | Collection | Outstanding | Due Date]
//     // -------------------------------------------------------

//     public function headings(): array
//     {
//         $base = [
//             'PO Number',
//             'PO Date',
//             'Sales Person',
//             'Sales Manager',
//             'School Name',
//             'School Code',
//         ];

//         // Inserted right after School Code for Marketing
//         if ($this->isMarketing) {
//             $base[] = 'School Email';
//             $base[] = 'School Phone';
//         }

//         $base[] = 'State';
//         $base[] = 'City';
//         $base[] = 'Lead Source';
//         $base[] = 'Status';
//         $base[] = 'PO Amount (₹)';

//         if (!$this->isMarketing) {
//             $base[] = 'Billed Amount (₹)';
//             $base[] = 'Pending PO (₹)';
//             $base[] = 'Collection (₹)';
//             $base[] = 'Outstanding (₹)';
//             $base[] = 'Delivery Due Date';
//         }

//         return $base;
//     }

//     public function map($invoice): array
//     {
//         $row = [
//             $invoice->po_number,
//             $invoice->invoice_date->format('d/m/Y'),
//             $invoice->user->username . ' (' . $invoice->user->emp_code . ')',
//             $invoice->user->reportiveTo?->username ?? '—',
//             $invoice->customer->name,
//             $invoice->customer->school_code,
//         ];

//         // Inserted right after school_code for Marketing
//         if ($this->isMarketing) {
//             $row[] = $invoice->customer->email        ?? '—';
//             $row[] = $invoice->customer->phone_number ?? '—';
//         }

//         $row[] = $invoice->customer->state;
//         $row[] = $invoice->customer->city;
//         $row[] = $invoice->customer->leadSource?->name ?? 'N/A';
//         $row[] = ucfirst($invoice->status);
//         $row[] = number_format($invoice->amount, 2);

//         if (!$this->isMarketing) {
//             $row[] = number_format($invoice->billing_amount, 2);
//             $row[] = number_format($invoice->amount - $invoice->billing_amount, 2);
//             $row[] = number_format($invoice->collected_amount, 2);
//             $row[] = number_format($invoice->outstanding_amount, 2);
//             $row[] = $invoice->delivery_due_date
//                 ? $invoice->delivery_due_date->format('d/m/Y')
//                 : '—';
//         }

//         return $row;
//     }

//     public function styles(Worksheet $sheet): array
//     {
//         // Marketing:  6 base + 2 contact + 5 common = 13 cols → M
//         // Non-Marketing: 6 base + 5 common + 5 financial = 16 cols → P
//         $lastCol = $this->isMarketing ? 'M' : 'P';

//         $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
//             'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
//             'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
//         ]);

//         // Freeze header row
//         $sheet->freezePane('A2');

//         return [];
//     }
// }


namespace App\Exports;

use App\Models\Invoice;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
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
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithEvents,
    ShouldAutoSize
{
    protected array $filters;
    protected User  $user;
    protected bool  $isMarketing;

    /**
     * Number of "invoice-level" columns (everything before the item columns).
     */
    protected int $invoiceColCount = 0;

    /**
     * Total number of columns in the export.
     */
    protected int $totalColCount = 0;

    /**
     * Running row pointer used while map() streams rows out.
     * Row 1 is the header, so data starts at row 2.
     */
    protected int $currentRow = 2;

    /**
     * Collected [startRow, endRow] pairs to merge after the sheet is built.
     * Only ranges with endRow > startRow actually need merging.
     */
    protected array $mergeRanges = [];

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

    public function query()
    {
        $teamIds = $this->user->teamMemberIds();

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

        $f = $this->filters;

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

        return $query->orderByDesc('invoice_date');
    }


    public function headings(): array
    {
        $base = [
            'PO Number',
            'PO Date',
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
            $this->invoiceColCount = count($base); // 18 invoice-level columns

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
            $this->invoiceColCount = count($base); // 13 invoice-level columns for marketing
        }

        $this->totalColCount = count($base);

        return $base;
    }

    /**
     * Returns MULTIPLE rows per invoice (one per invoice item).
     * Maatwebsite Excel supports this: if map() returns an array of
     * row-arrays, each sub-array becomes its own row in the sheet.
     */
    public function map($invoice): array
    {
        // Make sure column counts are set
        if ($this->totalColCount === 0) {
            $this->headings();
        }

        $invoiceCols = [
            $invoice->po_number,
            $invoice->invoice_date?->format('d/m/Y'),
            $invoice->user->username . ' (' . $invoice->user->emp_code . ')',
            $invoice->user->reportiveTo?->username ?? '—',
            $invoice->customer->name,
            $invoice->customer->school_code,
        ];

        if ($this->isMarketing) {
            $invoiceCols[] = $invoice->customer->email        ?? '—';
            $invoiceCols[] = $invoice->customer->phone_number ?? '—';
        }

        $invoiceCols[] = $invoice->customer->state;
        $invoiceCols[] = $invoice->customer->city;
        $invoiceCols[] = $invoice->customer->leadSource?->name ?? 'N/A';
        $invoiceCols[] = ucfirst($invoice->status);
        $invoiceCols[] = number_format($invoice->amount, 2);

        if (!$this->isMarketing) {
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
            $items = $invoice->invoiceItems;
            $itemRowCount = max($items->count(), 1);

            // Build dispatch qty map keyed by invoice_item_id
            $dispatchMap = [];
            foreach ($invoice->dispatches as $disp) {
                foreach ($disp->items as $di) {
                    $iid = $di->invoice_item_id;
                    if (!isset($dispatchMap[$iid])) $dispatchMap[$iid] = 0;
                    $dispatchMap[$iid] += (float) $di->quantity_dispatched;
                }
            }

            // Track the row range this invoice will occupy so we can merge later.
            $startRow = $this->currentRow;
            $endRow   = $startRow + $itemRowCount - 1;
            $this->mergeRanges[] = [$startRow, $endRow];
            $this->currentRow = $endRow + 1;

            // No items at all: one row, item columns blank (10 item cols).
            if ($items->isEmpty()) {
                return array_merge($invoiceCols, array_fill(0, 10, '—'));
            }

            $rows = [];
            foreach ($items as $i => $item) {
                $dispatched = (float) ($dispatchMap[$item->id] ?? 0);

                $discountPercent = (float) ($item->discount ?? 0);
                $mrp = (float) ($item->product?->price ?? 0);
                $savedRate = (float) ($item->rate ?? 0);

                if ($mrp > 0) {
                    $baseRate = $mrp;
                } elseif ($discountPercent > 0 && $savedRate > 0) {
                    $baseRate = round($savedRate / (1 - ($discountPercent / 100)), 2);
                } else {
                    $baseRate = $savedRate;
                }

                $discountAmount = $discountPercent > 0 ? round($baseRate * ($discountPercent / 100), 2) : 0.0;
                $netRate = max(0, round($baseRate - $discountAmount, 2));

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

                // Only the FIRST row carries the invoice-level values.
                // The rest are left blank; AfterSheet merges the cells so
                // Excel only needs (and only shows) the top-left value.
                $rows[] = $i === 0
                    ? array_merge($invoiceCols, $itemCols)
                    : array_merge(array_fill(0, count($invoiceCols), ''), $itemCols);
            }
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = Coordinate::stringFromColumnIndex($this->totalColCount ?: 28);

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
        ]);

        $sheet->freezePane('A2');

        return [];
    }

    /**
     * Merge the invoice-level columns down each item block, and add
     * a light border so the item rows for one invoice are visually grouped.
     */
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

                    // Thin bottom border to separate one invoice's block from the next.
                    $lastCol = Coordinate::stringFromColumnIndex($this->totalColCount ?: 28);
                    $sheet->getStyle("A{$endRow}:{$lastCol}{$endRow}")
                        ->getBorders()->getBottom()
                        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }
            },
        ];
    }
}
