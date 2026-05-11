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
            'customer:id,name,school_code,state,city,lead_source_id,email,phone_number',
            'customer.leadSource:id,name',
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

    // -------------------------------------------------------
    // Column order:
    //
    // PO Number | PO Date | SP | SM | School Name | School Code
    //   → [Marketing only: Email | Phone]
    // State | City | Lead Source | Status | PO Amount
    //   → [Non-Marketing: Billed | Pending PO | Collection | Outstanding | Due Date]
    // -------------------------------------------------------

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

        // Inserted right after School Code for Marketing
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
            $base[] = 'Pending PO (₹)';
            $base[] = 'Collection (₹)';
            $base[] = 'Outstanding (₹)';
            $base[] = 'Delivery Due Date';
        }

        return $base;
    }

    public function map($invoice): array
    {
        $row = [
            $invoice->po_number,
            $invoice->invoice_date->format('d/m/Y'),
            $invoice->user->username . ' (' . $invoice->user->emp_code . ')',
            $invoice->user->reportiveTo?->username ?? '—',
            $invoice->customer->name,
            $invoice->customer->school_code,
        ];

        // Inserted right after school_code for Marketing
        if ($this->isMarketing) {
            $row[] = $invoice->customer->email        ?? '—';
            $row[] = $invoice->customer->phone_number ?? '—';
        }

        $row[] = $invoice->customer->state;
        $row[] = $invoice->customer->city;
        $row[] = $invoice->customer->leadSource?->name ?? 'N/A';
        $row[] = ucfirst($invoice->status);
        $row[] = number_format($invoice->amount, 2);

        if (!$this->isMarketing) {
            $row[] = number_format($invoice->billing_amount, 2);
            $row[] = number_format($invoice->amount - $invoice->billing_amount, 2);
            $row[] = number_format($invoice->collected_amount, 2);
            $row[] = number_format($invoice->outstanding_amount, 2);
            $row[] = $invoice->delivery_due_date
                ? $invoice->delivery_due_date->format('d/m/Y')
                : '—';
        }

        return $row;
    }

    public function styles(Worksheet $sheet): array
    {
        // Marketing:  6 base + 2 contact + 5 common = 13 cols → M
        // Non-Marketing: 6 base + 5 common + 5 financial = 16 cols → P
        $lastCol = $this->isMarketing ? 'M' : 'P';

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
        ]);

        // Freeze header row
        $sheet->freezePane('A2');

        return [];
    }
}
