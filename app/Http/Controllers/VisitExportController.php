<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VisitExport;

class VisitExportController extends Controller
{
    public function __invoke(Request $request)
    {  
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        return Excel::download(new VisitExport($request->from, $request->to), "sangeeta-visits.xlsx");
    }
    
}
