<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\User;

class LoginController extends Controller
{
    public function create()
    {
        return auth()->check()
        ? redirect()->route('dashboard')
        : view('login');
    }

    public function store(Request $request)
    {

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);

        
        $user = User::where('email', $request->email)->first();

        if (isset($user) && $user->is_disable) {
            return back()->withErrors(['message' => 'User is disabled.']);
        } 
        
        if(!isset($user)) {
            return back()->withErrors(['message' => 'No such user found.']);
        }
        

        if (auth()->attempt($credentials, $request->has('remember'))) {

            $request->session()->regenerate();

            Log::create([
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'login_time' => now()
            ]);

            return redirect()->route('dashboard');
        }

        return back()->withErrors(['message' => 'The provided credentials are invalid.']);

    }
}
