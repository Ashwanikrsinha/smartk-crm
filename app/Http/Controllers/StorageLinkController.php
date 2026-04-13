<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class StorageLinkController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {  
        if(file_exists(public_path('storage'))){
          return redirect()->route('dashboard');
        }

        Artisan::call('storage:link');
        return redirect()->route('dashboard')->with('success', 'Storage Link Created');
        
    }
}
