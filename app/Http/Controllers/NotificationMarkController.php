<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationMarkController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {  
        if($request->isMethod('GET')){
            return abort(404);
        }

        auth()->user()
            ->unreadNotifications
            ->when($request->id, function ($query, $id) {
                return $query->where('id', $id);
            })
            ->markAsRead();
            
        return response()->noContent();

    }

}
