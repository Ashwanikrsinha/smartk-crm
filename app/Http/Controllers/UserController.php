<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Yajra\DataTables\DataTables;


class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            if(auth()->user()->isSalesManager()){
                $users = User::with('role', 'department')->select()->where('reportive_id', auth()->user()->id);
            }else{
                $users = User::with('role', 'department')->select();
            }

            return DataTables::of($users)
                   ->editColumn('status', function ($user) {
                        return $user->is_disable ? 'Disable' : 'Active';
                    })
                  ->addColumn('action', function ($user) {
                      return view('users.buttons')->with(['user' => $user]);
                  })->make(true);
        }
        return view('users.index');

    }

    public function create()
    {   
        $roles = Role::orderBy('name')->pluck('name', 'id');
        $users = User::active()->orderBy('username')->pluck('username', 'id');
        $departments = Department::orderBy('name')->pluck('name', 'id');

        return view('users.create', compact('roles', 'users', 'departments'));
    }

    public function store(UserStoreRequest $request)
    {

        $validatedData = $request->validated();
        $validatedData['password'] = bcrypt($request->password);
        $validatedData['is_disable'] = $request->has('is_disable');

        User::create($validatedData);

        return redirect()->route('users.index')->with('success', 'User Created');
    }

    public function show(User $user)
    {
        $logs = $user->logs()->paginate(50)->fragment('logs');
        return view('users.show', compact('user', 'logs'));
    }

    public function edit(User $user)
    {

        $roles = Role::orderBy('name')->pluck('name', 'id');
        $users = User::active()->orderBy('username')->pluck('username', 'id');
        $departments = Department::orderBy('name')->pluck('name', 'id');

        return view('users.edit', compact('user', 'roles', 'users', 'departments'));
    }

    public function update(UserUpdateRequest $request, User $user)
    {

        $validatedData = $request->validated();
        $validatedData['is_disable'] = $request->has('is_disable');


        if ($request->filled('password')) {

            $validatedData['password'] = bcrypt($request->password);
            $user->update($validatedData);

        } else {

            $user->update($request->except('password', 'password_confirmation'));
        }

        return back()->with('success', 'User Updated');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User Deleted');
    }
}
