<?php

namespace App\Exports;

use App\Models\Visit;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class VisitExport implements FromView, ShouldAutoSize, WithStyles
{
    public function __construct($from , $to)
    {
        $this->from = $from;
        $this->to = $to;
    }


   public function styles(Worksheet $styles) :array
   {
       return [
           1 => ['font' => ['bold' => true]]
       ];
   }

    /**
    * @return \Illuminate\Support\View
    */
    public function view(): View
    {
        $visits = Visit::with([
        'customer' => function ($query) { $query->select('id', 'name', 'segment_id'); }, 
        'user' => function ($query) { $query->select('id', 'username'); },
        'purpose', 
        'product',
        'customer.segment'
        ])
        ->whereBetween('visit_date', [$this->from, $this->to])
        ->get();
        
        return view('exports.visits', compact('visits'));
    }

}
