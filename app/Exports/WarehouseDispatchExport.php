<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class WarehouseDispatchExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithTitle
{
    protected array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Dispatch Queue';
    }

    public function headings(): array
    {
        return [
            'PO Number',
            'PO Date',
            'School Name',
            'School Code',
            'Product Type',
            'Product',
            'Total Qty',
            'Dispatched Qty',
            'Pending Qty',
            'Delivery Due Date',
            'Due Status',
            'Dispatch Number',
            'Dispatch Date',
            'Bilty Number',
            'Challan Number',
            'Vehicle Number',
            'Driver Name',
            'Vehicle Number',
            'Driver Phone',
            'Remarks',
        ];
    }

    public function collection()
    {
        $filters     = $this->filters;
        $year        = $filters['year']        ?? date('Y');
        $month       = $filters['month']       ?? null;
        $dateFrom    = $filters['date_from']   ?? null;
        $dateTo      = $filters['date_to']     ?? null;
        $duedateFrom = $filters['due_date_from'] ?? null;
        $duedateTo   = $filters['due_date_to']   ?? null;
        $productType = $filters['product_type'] ?? 'all';
        $productId   = $filters['product_id']   ?? 'all';

        $query = Invoice::with([
            'customer:id,name,school_code',
            'invoiceItems.product.category',
            'invoiceItems.unit',
            'dispatches',
            'dispatches.items',
            'dispatches.dispatchedBy:id,username',
        ])->where('status', Invoice::STATUS_APPROVED);

        if ($month) {
            $query->whereYear('invoice_date', substr($month, 0, 4))
                ->whereMonth('invoice_date', substr($month, 5, 2));
        } elseif ($dateFrom && $dateTo) {
            $query->whereBetween('invoice_date', [$dateFrom, $dateTo]);
        } else {
            $query->whereYear('invoice_date', $year);
        }

        if ($duedateFrom && $duedateTo) {
            $query->whereBetween('delivery_due_date', [$duedateFrom, $duedateTo]);
        }

        if ($productType !== 'all') {
            $query->whereHas('invoiceItems.product', fn($q) => $q->where('category_id', $productType));
        }

        if ($productId !== 'all') {
            $query->whereHas('invoiceItems', fn($q) => $q->where('product_id', $productId));
        }

        $allRows = $query->orderBy('delivery_due_date', 'asc')->get();

        // Flatten to one row per invoice-item (same logic as dashboard)
        $rows = collect();

        foreach ($allRows as $invoice) {
            $dispatched = \App\Models\DispatchItem::whereHas(
                'dispatch',
                fn($q) => $q->where('invoice_id', $invoice->id)
            )
                ->selectRaw('invoice_item_id, SUM(quantity_dispatched) as done')
                ->groupBy('invoice_item_id')
                ->get()
                ->keyBy('invoice_item_id');

            // foreach ($invoice->invoiceItems as $item) {
            //     $doneQty      = (float) ($dispatched[$item->id]->done ?? 0);
            //     $remainingQty = max(round((float) $item->quantity - $doneQty, 3), 0);

            //     // Apply item-level product type / product filters
            //     if ($productType !== 'all' && optional($item->product)->category_id != $productType) {
            //         continue;
            //     }
            //     if ($productId !== 'all' && $item->product_id != $productId) {
            //         continue;
            //     }

            //     // Skip fully dispatched items (mirrors dashboard)
            //     if ($remainingQty <= 0) {
            //         continue;
            //     }

            //     $dueDate   = $invoice->delivery_due_date;
            //     $dueStatus = '—';
            //     if ($dueDate) {
            //         $dueStatus = $dueDate->isPast() ? 'OVERDUE' : 'On Track';
            //     }

            //     $rows->push([
            //         'po_number'     => $invoice->po_number,
            //         'po_date'       => $invoice->invoice_date->format('d M Y'),
            //         'school_name'   => $invoice->customer->name ?? '—',
            //         'school_code'   => $invoice->customer->school_code ?? '—',
            //         'product_type'  => optional($item->product->category)->name ?? '—',
            //         'product'       => optional($item->product)->name ?? '—',
            //         'total_qty'     => (float) $item->quantity,
            //         'dispatched'    => $doneQty,
            //         'pending'       => $remainingQty,
            //         'due_date'      => $dueDate ? $dueDate->format('d M Y') : '—',
            //         'due_status'    => $dueStatus,
            //         'dispatch_number'    => $invoice->dispatches->dispatch_number ?? '—',
            //         'dispatch_date'    => $invoice->dispatches->dispatch_date ?? '—',
            //         'bilty_number'    => $invoice->dispatches->bilty_number->format('d M Y') ?? '—',
            //         'challan_number'    => $invoice->dispatches->challan_number ?? '—',
            //         'vehicle_number'    => $invoice->dispatches->vehicle_number ?? '—',
            //         'driver_name'    => $invoice->dispatches->driver_name ?? '—',
            //         'vehicle_number'    => $invoice->dispatches->vehicle_number ?? '—',
            //         'driver_phone'    => $invoice->dispatches->driver_phone ?? '—',
            //         'dispatched_by'    => $invoice->dispatches->dispatchedBy->username ?? '—',
            //         'remarks'    => $invoice->dispatches->remarks ?? '—',
            //     ]);
            // }
            foreach ($invoice->invoiceItems as $item) {
                $doneQty      = (float) ($dispatched[$item->id]->done ?? 0);
                $remainingQty = max(round((float) $item->quantity - $doneQty, 3), 0);
            
                if ($productType !== 'all' && optional($item->product)->category_id != $productType) {
                    continue;
                }
                if ($productId !== 'all' && $item->product_id != $productId) {
                    continue;
                }
                if ($remainingQty <= 0) {
                    continue;
                }
            
                $dueDate   = $invoice->delivery_due_date;
                $dueStatus = '—';
                if ($dueDate) {
                    $dueStatus = $dueDate->isPast() ? 'OVERDUE' : 'On Track';
                }
            
                // Pick the latest dispatch that actually shipped this item
                $lastDispatch = $invoice->dispatches
                    ->sortByDesc('dispatch_date')
                    ->first(fn($d) => $d->items->contains('invoice_item_id', $item->id));
            
                $rows->push([
                    'po_number'      => $invoice->po_number,
                    'po_date'        => $invoice->invoice_date->format('d M Y'),
                    'school_name'    => $invoice->customer->name ?? '—',
                    'school_code'    => $invoice->customer->school_code ?? '—',
                    'product_type'   => optional($item->product->category)->name ?? '—',
                    'product'        => optional($item->product)->name ?? '—',
                    'total_qty'      => (float) $item->quantity,
                    'dispatched'     => $doneQty,
                    'pending'        => $remainingQty,
                    'due_date'       => $dueDate ? $dueDate->format('d M Y') : '—',
                    'due_status'     => $dueStatus,
                    'dispatch_number'=> $lastDispatch->dispatch_number ?? '—',
                    'dispatch_date'  => $lastDispatch?->dispatch_date?->format('d M Y') ?? '—',
                    'bilty_number'   => $lastDispatch->bilty_number ?? '—',
                    'challan_number' => $lastDispatch->challan_number ?? '—',
                    'vehicle_number' => $lastDispatch->vehicle_number ?? '—',
                    'driver_name'    => $lastDispatch->driver_name ?? '—',
                    'driver_phone'   => $lastDispatch->driver_phone ?? '—',
                    'dispatched_by'  => $lastDispatch->dispatchedBy->username ?? '—',
                    'remarks'        => $lastDispatch->remarks ?? '—',
                ]);
            }
        }

        return $rows;
    }

    public function map($row): array
    {
        return array_values($row);
    }

    public function styles(Worksheet $sheet): array
    {
        // Header row styling
        $sheet->getStyle('A1:T1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F2044'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Highlight OVERDUE rows in light red
        // We do this dynamically after data is written — PhpSpreadsheet
        // doesn't support conditional formatting per-row easily here,
        // so we'll use a zebra stripe for readability instead.
        $sheet->getStyle('A1:K1')->getAlignment()
            ->setWrapText(false);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
