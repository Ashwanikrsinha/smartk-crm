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
        $scope = $this->filters['scope'] ?? 'pending';
        return $scope === 'all' ? 'All Dispatches' : 'Dispatch Queue';
    }

    public function headings(): array
    {
        return [
            'PO Number',
            'PO Date',
            'Co-branding',
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
            'No. of Boxes',
            'Total Weight (kg)',
            'Bilty Number',
            'POD Slip',
            'Challan Number',
            'Vehicle Number',
            'Driver Name',
            'Driver Phone',
            'Dispatched By',
            'Remarks',
        ];
    }

    public function collection()
    {
        $filters     = $this->filters;
        $scope       = $filters['scope']       ?? 'pending'; // 'pending' or 'all'
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

        // Flatten to one row per invoice-item
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

            foreach ($invoice->invoiceItems as $item) {
                $doneQty      = (float) ($dispatched[$item->id]->done ?? 0);
                $remainingQty = max(round((float) $item->quantity - $doneQty, 3), 0);

                if ($productType !== 'all' && optional($item->product)->category_id != $productType) {
                    continue;
                }
                if ($productId !== 'all' && $item->product_id != $productId) {
                    continue;
                }

                // If scope is pending, only show items that have remaining qty
                if ($scope === 'pending' && $remainingQty <= 0) {
                    continue;
                }

                $dueDate   = $invoice->delivery_due_date;
                $dueStatus = '—';
                if ($dueDate) {
                    $dueStatus = $dueDate->isPast() ? ($remainingQty > 0 ? 'OVERDUE' : 'Completed') : ($remainingQty > 0 ? 'On Track' : 'Completed');
                }

                // Pick the latest dispatch that actually shipped this item (or invoice latest dispatch)
                $sortedDispatches = $invoice->dispatches->sort(function ($a, $b) {
                    $dateA = $a->dispatch_date?->format('Y-m-d') ?? '';
                    $dateB = $b->dispatch_date?->format('Y-m-d') ?? '';
                    if ($dateA === $dateB) {
                        return $b->id <=> $a->id;
                    }
                    return $dateB <=> $dateA;
                });

                $lastDispatch = $sortedDispatches
                    ->first(fn($d) => $d->items->contains('invoice_item_id', $item->id))
                    ?? $sortedDispatches->first();

                $rows->push([
                    'po_number'       => $invoice->po_number,
                    'po_date'         => $invoice->invoice_date->format('d M Y'),
                    'co_branding'     => $invoice->co_branding ? 'Yes' : 'No',
                    'school_name'     => $invoice->customer->name ?? '—',
                    'school_code'     => $invoice->customer->school_code ?? '—',
                    'product_type'    => optional($item->product->category)->name ?? '—',
                    'product'         => optional($item->product)->name ?? '—',
                    'total_qty'       => (float) $item->quantity,
                    'dispatched'      => $doneQty,
                    'pending'         => $remainingQty,
                    'due_date'        => $dueDate ? $dueDate->format('d M Y') : '—',
                    'due_status'      => $dueStatus,
                    'dispatch_number' => $lastDispatch->dispatch_number ?? '—',
                    'dispatch_date'   => $lastDispatch?->dispatch_date?->format('d M Y') ?? '—',
                    'no_of_boxes'     => $lastDispatch?->no_of_boxes ?? '—',
                    'total_weight'    => $lastDispatch?->total_weight ? $lastDispatch->total_weight : '—',
                    'bilty_number'    => $lastDispatch->bilty_number ?? '—',
                    'pod_slip'        => $lastDispatch?->pod_slip ? 'Uploaded' : '—',
                    'challan_number'  => $lastDispatch->challan_number ?? '—',
                    'vehicle_number'  => $lastDispatch->vehicle_number ?? '—',
                    'driver_name'     => $lastDispatch->driver_name ?? '—',
                    'driver_phone'    => $lastDispatch->driver_phone ?? '—',
                    'dispatched_by'   => $lastDispatch->dispatchedBy->username ?? '—',
                    'remarks'         => $lastDispatch->remarks ?? '—',
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
        $sheet->getStyle('A1:X1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F2044'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('A1:K1')->getAlignment()
            ->setWrapText(false);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
