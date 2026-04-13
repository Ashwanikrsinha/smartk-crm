<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;

class VisitChartController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {  
        $this->authorize('viewAny', Visit::class);
        
        if (!$request->ajax()) {
            return abort(404);
        }

        for ($m = 1; $m <= 12; $m++) {
            $months[] = date('F', mktime(0,0,0, $m, 1, date('Y')));
        }

        $visits = Visit::selectRaw('
            COUNT(visit_date) AS visit_count,
            MONTH(visit_date) AS visit_month
            ')
            ->when($request->purpose_id, function ($query, $purpose){
                return $query->where('purpose_id', $purpose);
            })
            ->when($request->year, function ($query, $year){
                return $query->whereYear('visit_date', $year); 
            })
            ->groupByRaw('MONTH(visit_date)')
            ->orderByRaw('MONTH(visit_date) ASC')
            ->get();

        
        if (isset($visits) & $visits->count() > 0) {
            foreach($visits as $visit){
                $month = $months[$visit->visit_month - 1];
                $visits_count[$month] = $visit->visit_count;
            }
        } else {
            $visits_count = [];
        }
        

        return [
            'months' => $months,
            'visits_count' => $visits_count
        ];

    }
}
