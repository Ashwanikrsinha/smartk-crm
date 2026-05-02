<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\PoLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PoLogExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected ?int $invoiceId;
    protected array $filters;

    /**
     * @param int|null $invoiceId  Null = export ALL logs (for reports page)
     * @param array $filters
     */
    public function __construct(?int $invoiceId = null, array $filters = [])
    {
        $this->invoiceId = $invoiceId;
        $this->filters   = $filters;
    }

    public function title(): string
    {
        return $this->invoiceId ? 'PO Log' : 'All PO Logs';
    }

    public function query()
    {
        $query = PoLog::with([
            'invoice.customer:id,name,school_code,state,city,lead_source_id',
            'invoice.user:id,username,reportive_id',
            'invoice.user.reportiveTo:id,username',
            'user:id,username',
        ])->orderByDesc('created_at');

        if ($this->invoiceId) {
            $query->where('invoice_id', $this->invoiceId);
        } else {
            // Scope by team for non-admin
            $teamIds = auth()->user()->teamMemberIds();
            $query->whereHas('invoice', function($q) use ($teamIds) {
                $q->whereIn('user_id', $teamIds);

                // Apply same filters as PurchaseOrdersExport
                $f = $this->filters;
                if (!empty($f['sp_id']))       $q->where('user_id', $f['sp_id']);
                if (!empty($f['school_id']))   $q->where('customer_id', $f['school_id']);
                if (!empty($f['status']))      $q->where('status', $f['status']);

                if (!empty($f['lead_source_id'])) {
                    $q->whereHas('customer', fn($c) => $c->where('lead_source_id', $f['lead_source_id']));
                }
                if (!empty($f['state'])) {
                    $q->whereHas('customer', fn($c) => $c->where('state', $f['state']));
                }
                if (!empty($f['month'])) {
                    $q->whereYear('invoice_date',  substr($f['month'], 0, 4))
                      ->whereMonth('invoice_date', substr($f['month'], 5, 2));
                } elseif (!empty($f['date_from']) && !empty($f['date_to'])) {
                    $q->whereBetween('invoice_date', [$f['date_from'], $f['date_to']]);
                } elseif (!empty($f['year'])) {
                    $q->whereYear('invoice_date', $f['year']);
                }
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Time',
            'PO Number',
            'School Name',
            'School Code',
            'Sales Person',
            'Sales Manager',
            'Action',
            'Amount (₹)',
            'Payment Mode',
            'Billing Source',
            'Reference No.',
            'PO Amount (₹)',
            'Billed Amount (₹)',
            'Pending PO (₹)',
            'Collected (₹)',
            'Outstanding (₹)',
            'Entered By',
            'Remarks',
        ];
    }

    public function map($log): array
    {
        $invoice = $log->invoice;
        $sp      = $invoice?->user;

        // Get SM: the reportive_to of the SP
        $sm = $sp?->reportiveTo;

        return [
            $log->created_at->format('d/m/Y'),
            $log->created_at->format('h:i A'),
            $invoice?->po_number       ?? '—',
            $invoice?->customer?->name ?? '—',
            $invoice?->customer?->school_code ?? '—',
            $sp?->username             ?? '—',
            $sm?->username             ?? '—',    // ← Manager Name column
            $log->action_label,
            $log->amount > 0 ? number_format($log->amount, 2) : '—',
            $log->payment_mode         ? strtoupper($log->payment_mode) : '—',
            $log->billing_source       ? ucfirst($log->billing_source)  : '—',
            $log->reference_number     ?? '—',
            number_format($log->snapshot_po_amount,     2),
            number_format($log->snapshot_billed_amount, 2),
            number_format($log->snapshot_pending_po,    2),
            number_format($log->snapshot_collected,     2),
            number_format($log->snapshot_outstanding,   2),
            $log->user?->username      ?? '—',
            $log->remarks              ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:S1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F2044']],
        ]);
        $sheet->freezePane('A2');
        return [];
    }
}
