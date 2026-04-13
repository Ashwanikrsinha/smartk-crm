<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use App\Http\Requests\TargetRequest;
use Yajra\DataTables\DataTables;

class TargetController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Target::class, 'target');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $targets = Target::with('user')->selectRaw(
                '*, 
                (SELECT COUNT(visit_date) FROM visits 
                WHERE user_id = targets.user_id 
                AND 
                visit_date BETWEEN targets.start_date AND targets.end_date) 
                AS total_visits'
            );
                
              
            return DataTables::of($targets)
                    ->editColumn('target', function ($target) {
                        return $target->target .'/' . $target->total_visits;
                    })
                    ->editColumn('start_date', function ($target) {
                        return $target->start_date->format('d M, Y');
                    })
                    ->editColumn('end_date', function ($target) {
                        return $target->end_date->format('d M, Y');
                    })
                    ->editColumn('complete', function ($target) {
                        return $target->total_visits * 100 / $target->target . '%';
                    })
                  ->addColumn('action', function ($target) {
                      return view('targets.buttons')->with(['target' => $target]);
                  })->make(true);
        }

        return view('targets.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::active()->orderBy('username')->pluck('username', 'id');
        return view('targets.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TargetRequest $request)
    {   
        Target::create($request->validated());
        return  back()->with('success', 'Target Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Target $target
     * @return \Illuminate\Http\Response
     */
    public function show(Target $target)
    {
        $query = Visit::where('user_id', $target->user_id)
            ->whereBetween('visit_date', [$target->start_date, $target->end_date]);

        $visits = $query->paginate()->fragment('visits');
        $total_visits = $query->count();

        return view('targets.show', compact('target', 'visits', 'total_visits'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Target $target
     * @return \Illuminate\Http\Response
     */
    public function edit(Target $target)
    {
        $users = User::active()->orderBy('username')->pluck('username', 'id');
        return view('targets.edit', compact('target', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Target $target
     * @return \Illuminate\Http\Response
     */
    public function update(TargetRequest $request, Target $target)
    {
        $target->update($request->validated());
        return back()->with('success', 'Target Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Target $target
     * @return \Illuminate\Http\Response
     */
    public function destroy(Target $target)
    {
        $target->delete();
        return back()->with('success', 'Target Deleted');
    }
}
