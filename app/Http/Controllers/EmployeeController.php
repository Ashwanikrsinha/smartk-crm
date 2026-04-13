<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Requests\EmployeeRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Employee::class, 'employee');
    }

   
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $employees = Employee::with('department')->select();
  
            return DataTables::of($employees)
                  ->addColumn('action', function ($employee) {
                      return view('employees.buttons')->with(['employee' => $employee]);
                  })->make(true);
        }
        return view('employees.index');

    }

    public function create()
    {
        $marital_statuses = Employee::maritalStatus();
        $roles = Role::orderBy('name')->pluck('name', 'id');
        $departments = Department::orderBy('name')->pluck('name', 'id');
        $users = User::active()->orderBy('username')->pluck('username', 'id');
        $states = State::orderBy('name')->pluck('name');


        return view('employees.create', compact('departments', 'marital_statuses', 'roles', 'users', 'states'));
    }

    public function store(EmployeeRequest $request)
    {
        
        DB::transaction(function() use($request){

            Employee::create($request->only([
                'name',
                'department_id',
                'email',
                'phone_number',
                'state',
                'city',
                'address',
                'qualification',
                'salary',
                'hr_allowance',
                'convey_allowance',
                'spi_allowance',
                'joining_date',
                'resign_date',
                'resign_reason'
            ]));

            if ($request->has('can_login')) {

                User::createUser($request->only([
                    'username',
                    'email',
                    'role_id',
                    'department_id',
                    'reportive_id',
                    'is_disable',
                    'password'
                ]));

            }

        });    
        

        return redirect()->route('employees.index')->with('success', 'Employee Created');
    }

    public function show(Employee $employee)
    {
        return abort(404);
    }

    public function edit(Employee $employee)
    {
        
        $marital_statuses = Employee::maritalStatus();
        $departments = Department::orderBy('name')->pluck('name', 'id');
        $roles = Role::orderBy('name')->pluck('name', 'id');
        $users = User::active()->orderBy('username')->pluck('username', 'id');
        $states = State::orderBy('name')->pluck('name');

        return view('employees.edit', compact('departments', 'marital_statuses', 'employee', 'roles', 'users', 'states'));
    }

    public function update(EmployeeRequest $request, Employee $employee)
    {
               
        DB::transaction(function() use($request, $employee){

            $employee->update($request->only([
                'name',
                'department_id',
                'email',
                'phone_number',
                'state',
                'city',
                'address',
                'qualification',
                'salary',
                'hr_allowance',
                'convey_allowance',
                'spi_allowance',
                'joining_date',
                'resign_date',
                'resign_reason'
            ]));

            if ($request->has('can_login')) {

                User::createUser($request->only([
                    'username',
                    'email',
                    'role_id',
                    'department_id',
                    'reportive_id',
                    'is_disable',
                    'password',
                ]));
 
            }

        });    
        

        return back()->with('success', 'Employee Updated');
    }

    public function destroy(Employee $employee)
    {   
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee Deleted');
    }
}
