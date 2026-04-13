<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class VisitSummaryController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {  
        $this->authorize('viewAny', User::class);

        if(!$request->ajax()){
            return abort(404);
        }

        $users = User::select('id', 'username', 'is_disable')
            ->active()
            ->withCount([
            'todayVisits',
            'yesterdayVisits',
            'lastSevenDayVisits',
            'currentMonthVisits',
            'lastMonthVisits'
            ])
            ->orderBy('username')
            ->get();
            
        return view('reports.visit-summary', compact('users'));

    }
}
