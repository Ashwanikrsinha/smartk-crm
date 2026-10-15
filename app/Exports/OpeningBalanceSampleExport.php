<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\SchoolOpeningBalance;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// ─── Main export (two sheets) ──────────────────────────────────────────────
class OpeningBalanceSampleExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new ObTemplateSheet(),
            new ObReferenceGuideSheet(),
        ];
    }
}

// ─── Sheet 1: Template ─────────────────────────────────────────────────────
class ObTemplateSheet implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Opening Balance Template';
    }

    public function headings(): array
    {
        return [
            'School Code',           // required — must match existing school code in CRM
            'School Name',           // for reference/verification
            'SP Employee Code',      // optional — emp_code of the sales person
            'Opening Amount',        // total carry-forward amount (₹)
            'Already Collected',     // amount already paid from this opening balance (₹), 0 if nothing collected yet
            'Notes',                 // any remarks
        ];
    }

    public function collection(): Collection
    {
        // Provide one sample row
        return collect([
            [
                'SCH-2025-0001',
                'Example Public School',
                'EMP-001',
                300000,
                50000,
                'Carry forward from 2024-25 session',
            ],
        ]);
    }

    public function styles(Worksheet $sheet): array
    {
        // Header row styling
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F2044'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Required columns highlight (A, D)
        $sheet->getStyle('A2:A2')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3CD']],
        ]);

        // Note row
        $sheet->setCellValue('A4', '⚠ School Code is mandatory and must exactly match the code in the CRM.');
        $sheet->setCellValue('A5', '⚠ Opening Amount can be positive (dues to collect) or negative (advance/credit balance).');
        $sheet->setCellValue('A6', '⚠ Already Collected should be 0 if no payment has been made yet.');
        $sheet->setCellValue('A7', '⚠ SP Employee Code is optional. Leave blank if unknown.');
        $sheet->getStyle('A4:A7')->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF8B0000'));

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

// ─── Sheet 2: Reference Guide ─────────────────────────────────────────────
class ObReferenceGuideSheet implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Reference - SP List';
    }

    public function headings(): array
    {
        return ['Employee Code', 'Employee Name', 'Role'];
    }

    public function collection(): Collection
    {
        return User::active()
            ->with('role')
            ->SalesPersonOrManager()
            ->orderBy('username')
            ->get()
            ->map(fn($u) => [
                $u->emp_code ?? '—',
                $u->username,
                optional($u->role)->name ?? '—',
            ]);
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '155724'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
