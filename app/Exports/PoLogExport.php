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

    /**
     * @param int|null $invoiceId  Null = export ALL logs (for reports page)
     */
    public function __construct(?int $invoiceId = null)
    {
        $this->invoiceId = $invoiceId;
    }

    public function title(): string
    {
        return $this->invoiceId ? 'PO Log' : 'All PO Logs';
    }

    public function query()
    {
        $query = PoLog::with([
            'invoice.customer:id,name,school_code',
            'invoice.user:id,username',
            'user:id,username',
        ])->orderByDesc('created_at');

        if ($this->invoiceId) {
            $query->where('invoice_id', $this->invoiceId);
        } else {
            // Scope by team for non-admin
            $teamIds = auth()->user()->teamMemberIds();
            $query->whereHas('invoice', fn($q) => $q->whereIn('user_id', $teamIds));
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
            'A: PO Amount (₹)',
            'B: Billed Amount (₹)',
            'C: Pending PO (₹)',
            'D: Collected (₹)',
            'E: Outstanding (₹)',
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
