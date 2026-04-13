<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;

class VisitAgingController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {  
        
        $visits = Visit::with(['customer' => function($query){ $query->select('id', 'name'); } ])
                ->selectRaw('visit_date, customer_id, DATEDIFF(CURRENT_DATE(), visit_date) AS total_days')
                ->groupBy('visit_date', 'customer_id')
                ->orderBy('visit_date', 'ASC')
                ->paginate(100);

        return view('reports.visits-aging', compact('visits'));
    }
}
