<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PurchaseOrdersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected array $filters;
    protected User  $user;
    protected bool  $isMarketing;

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
            'customer:id,name,school_code,state,city,lead_source_id',
            'customer.leadSource:id,name',
        ])->whereIn('user_id', $teamIds);

        $f = $this->filters;

        if (!empty($f['sp_id']))       $query->where('user_id', $f['sp_id']);
        if (!empty($f['school_id']))   $query->where('customer_id', $f['school_id']);
        if (!empty($f['status']))      $query->where('status', $f['status']);

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
        // Marketing: no Collected or Outstanding columns
        $base = [
            'PO Number',
            'PO Date',
            'Sales Person',
            'Sales Manager',
            'School Name',
            'School Code',
            'State',
            'City',
            'Lead Source',
            'Status',
            'PO Amount (₹)',
            'Billed Amount (₹)',
            'Pending PO (₹)',
        ];

        if (!$this->isMarketing) {
            $base[] = 'Collection (₹)';
            $base[] = 'Outstanding (₹)';
        }

        $base[] = 'Delivery Due Date';

        return $base;
    }

    public function map($invoice): array
    {
        $row = [
            $invoice->po_number,
            $invoice->invoice_date->format('d/m/Y'),
            $invoice->user->username . ' (' . $invoice->user->emp_code . ')',
            $invoice->user->reportiveTo?->username,
            $invoice->customer->name,
            $invoice->customer->school_code,
            $invoice->customer->state,
            $invoice->customer->city,
            $invoice->customer->leadSource?->name ?? 'N/A',
            ucfirst($invoice->status),
            number_format($invoice->amount, 2),
            number_format($invoice->billing_amount, 2),
            number_format($invoice->amount - $invoice->billing_amount, 2),
        ];

        if (!$this->isMarketing) {
            $row[] = number_format($invoice->collected_amount, 2);
            $row[] = number_format($invoice->outstanding_amount, 2);
        }

        $row[] = $invoice->delivery_due_date
            ? $invoice->delivery_due_date->format('d/m/Y')
            : '';

        return $row;
    }

    public function styles(Worksheet $sheet): array
    {
        // Last column letter depends on whether Marketing (N) or others (P)
        $lastCol = $this->isMarketing ? 'N' : 'P';

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
        ]);

        // Freeze header row
        $sheet->freezePane('A2');

        return [];
    }
}
