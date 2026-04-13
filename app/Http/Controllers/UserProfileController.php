<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    public function show(Request $request, User $user)
    {
        $marital_statuses = User::maritalStatus();
        return view('users.profile', compact('user', 'marital_statuses'));
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
          'username' => 'bail|required',
          'email' => 'bail|required|email|'.Rule::unique('users', 'email')->ignore($user),
          'password' => 'nullable|confirmed|min:5',
        ]);
        

        if ($request->filled('password')) {

            $validatedData['password'] = bcrypt($request->password);
            $user->update($validatedData);

        } else {
            $user->update($request->except('password', 'password_confirmation'));
        }


        return back()->with('success', 'Profile Updated');
    }
}
