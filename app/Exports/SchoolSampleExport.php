<?php

namespace App\Exports;

use App\Models\Designation;
use App\Models\LeadSource;
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

class SchoolSampleExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new SchoolTemplateSheet(),
            new SchoolReferenceGuideSheet(),
        ];
    }
}

class SchoolTemplateSheet implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Schools Template';
    }

    public function headings(): array
    {
        return [
            'School Name',
            'Mobile Number',
            'State',
            'City',
            'Pin Code',
            'Email ID',
            'Address',
            'GSTIN',
            'Lead Source',
            'Description',
            'Contact Person',
            'Contact Number',
            'Designation',
            'Employee Code',
        ];
    }

    public function collection(): Collection
    {
        $firstLeadSource = LeadSource::first();
        $firstDesignation = Designation::first();
        $leadSourceVal = $firstLeadSource ? $firstLeadSource->id : '1';
        $designationVal = $firstDesignation ? $firstDesignation->id : '1';

        $firstEmp = User::whereNotNull('emp_code')->first();
        $sampleEmpCode = $firstEmp ? $firstEmp->emp_code : 'EMP-2026-0001';

        return collect([
            [
                'School Name'    => 'Delhi Public School',
                'Mobile Number'  => '9876543210',
                'State'          => 'Delhi',
                'City'           => 'New Delhi',
                'Pin Code'       => '110001',
                'Email ID'       => 'info@dpsdelhi.edu.in',
                'Address'        => 'Sector 12, RK Puram',
                'GSTIN'          => '07AAAAA0000A1Z5',
                'Lead Source'    => $leadSourceVal, // ID or Name
                'Description'    => 'CBSE Senior Secondary School',
                'Contact Person' => 'Dr. Rajesh Sharma',
                'Contact Number' => '9876543211',
                'Designation'    => $designationVal, // ID or Name
                'Employee Code'  => $sampleEmpCode,
            ],
            [
                'School Name'    => "St. Xavier's High School",
                'Mobile Number'  => '9812345678',
                'State'          => 'Maharashtra',
                'City'           => 'Mumbai',
                'Pin Code'       => '400001',
                'Email ID'       => 'admin@stxaviers.edu',
                'Address'        => 'Fort Campus, Mahapalika Marg',
                'GSTIN'          => '',
                'Lead Source'    => 'Direct Call',
                'Description'    => 'ICSE School',
                'Contact Person' => 'Father Joseph',
                'Contact Number' => '9812345679',
                'Designation'    => 'Principal',
                'Employee Code'  => '',
            ],
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'], // Navy blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(26);

        // Style data rows
        $sheet->getStyle('A2:N3')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'E5E7EB'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        return [];
    }
}

class SchoolReferenceGuideSheet implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Reference Guide (IDs & Names)';
    }

    public function headings(): array
    {
        return [
            'Category',
            'ID / Code',
            'Name / Title',
            'Usage Notes',
        ];
    }

    public function collection(): Collection
    {
        $rows = collect();

        // Lead Sources
        $leadSources = LeadSource::all();
        foreach ($leadSources as $ls) {
            $rows->push([
                'Category'    => 'Lead Source',
                'ID / Code'   => (string) $ls->id,
                'Name / Title'=> $ls->name,
                'Usage Notes' => 'Use either ID (' . $ls->id . ') or Name ("' . $ls->name . '") in Lead Source column',
            ]);
        }

        // Designations
        $designations = Designation::all();
        foreach ($designations as $des) {
            $rows->push([
                'Category'    => 'Designation',
                'ID / Code'   => (string) $des->id,
                'Name / Title'=> $des->name,
                'Usage Notes' => 'Use either ID (' . $des->id . ') or Name ("' . $des->name . '") in Designation column',
            ]);
        }

        // Employees (For Admin / BM reference)
        $users = User::with('role')->where('is_disable', 0)->get();
        foreach ($users as $u) {
            $roleName = $u->role?->name ?? 'User';
            $rows->push([
                'Category'    => 'Employee (Admin only)',
                'ID / Code'   => $u->emp_code ?? (string) $u->id,
                'Name / Title'=> $u->username . " ({$roleName})",
                'Usage Notes' => 'Admin bulk upload: Set Employee Code to assign school to this employee',
            ]);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F766E'], // Teal
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(24);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $sheet->getStyle("A2:D{$highestRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'E5E7EB'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        return [];
    }
}
