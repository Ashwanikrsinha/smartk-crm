<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;

class LogoutController extends Controller
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
        
        $log = Log::where('user_id', auth()->id())
            ->whereNull('logout_time')
            ->where('id', function($query){
                $query->selectRaw('MAX(id)')->from('logs');
            })
            ->first();
        
        if(isset($log)){
           $log->update(['logout_time' => now()]);   
        }

       auth()->logout();
       $request->session()->invalidate();
       $request->session()->regenerateToken();
       return redirect()->route('login');
    }
}
