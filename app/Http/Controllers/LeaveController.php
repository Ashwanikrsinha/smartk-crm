<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\LeaveRequest;
use App\Notifications\NewLeaveNotification;
use App\Notifications\LeaveUpdateNotification;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Leave::class, 'leave');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $leaves = Leave::with('user')->where(function($query){
                $query->where('user_id', auth()->id())
                    ->orWhereHas('user', function($q){ 
                        $q->where('reportive_id', auth()->id()); 
                    });   
            })->select();

            return DataTables::of($leaves)
                    ->editColumn('leave_number', function ($leave) {
                        return "L-{$leave->leave_number}";
                    })
                    ->editColumn('from_date', function ($leave) {
                        return $leave->from_date->format('d M, Y');
                    })
                    ->editColumn('to_date', function ($leave) {
                        return $leave->to_date->format('d M, Y');
                    })
                    ->editColumn('leave_days', function ($leave) {
                        return $leave->to_date->diffInDays($leave->from_date) + 1;
                    })
                    ->editColumn('type', function ($leave) {
                        return ucwords($leave->type);
                    })
                    ->editColumn('status', function ($leave) {
                        return ucwords($leave->status);
                    })
                  ->addColumn('action', function ($leave) {
                      return view('leaves.buttons')->with(['leave' => $leave]);
                  })->make(true);
        }

        return view('leaves.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = Leave::types();
        $statuses = Leave::leaveStatus();
        return view('leaves.create', compact('types', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeaveRequest $request)
    {
        $leave = Leave::create([
            'leave_number' => Leave::leaveNumber(),
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'type' => $request->type,
            'user_id' => auth()->id(),
            'status' => 'pending',
            'comment' => $request->comment
        ]);

        if(isset(auth()->user()->reportive_id)){
            $user = User::findOrFail(auth()->user()->reportive_id);
            $user->notify(new NewLeaveNotification($leave));
        }
        
        return  back()->with('success', 'Leave Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Leave $leave
     * @return \Illuminate\Http\Response
     */
    public function show(Leave $leave)
    {
        return view('leaves.show', compact('leave'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Leave $leave
     * @return \Illuminate\Http\Response
     */
    public function edit(Leave $leave)
    {
        $types = Leave::types();
        $statuses = Leave::leaveStatus();
        return view('leaves.edit', compact('leave', 'types', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Leave $leave
     * @return \Illuminate\Http\Response
     */
    public function update(LeaveRequest $request, Leave $leave)
    {

        $leave->update([
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'type' => $request->type,
            'status' => $request->status,
            'comment' => $request->comment
        ]);

        $leave->load('user');

        $leave->user->notify(new LeaveUpdateNotification($leave));
    
        return  back()->with('success', 'Leave Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Leave $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy(Leave $leave)
    {
        $leave->delete();
        return back()->with('success', 'Leave Deleted');
    }
}
