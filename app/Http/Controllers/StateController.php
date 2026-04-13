<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;


class StateController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(State::class, 'state');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $states = State::select();
  
            return Datatables::of($states)
                  ->addColumn('action', function ($state) {
                      return view('states.buttons')->with(['state' => $state ]);
                  })->make(true);
        }

        return view('states.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('states.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|max:100|unique:states']);
       
        State::create(['name' => $request->name]);

        return redirect()->route('states.index')->with('success', 'State Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Modes\State $state
     * @return \Illuminate\Http\Response
     */
    public function show(State $state)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Modes\State $state
     * @return \Illuminate\Http\Response
     */
    public function edit(State $state)
    {
        return view('states.edit', ['state' => $state]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Modes\State $state
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, State $state)
    {
        $request->validate([
            'name' => 'required|max:100|'.Rule::unique('states')->ignore($state)
        ]);
       
        $state->update(['name' => $request->name]);

        return  redirect()->route('states.index')->with('success', 'State Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Modes\State $state
     * @return \Illuminate\Http\Response
     */
    public function destroy(State $state)
    {
        $state->delete();
        return back()->with('success', 'State Deleted');
    }
}
