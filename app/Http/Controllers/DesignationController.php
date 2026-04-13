<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class DesignationController extends Controller
{

    public function __construct()
    {
       $this->authorizeResource(Designation::class, 'designation');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $designations = Designation::select();
  
            return DataTables::of($designations)
                  ->addColumn('action', function ($designation) {
                      return view('designations.buttons')->with(['designation' => $designation]);
                  })->make(true);
        }

        return view('designations.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('designations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:designations|max:100',
        ]);

        Designation::create(['name' => $request->name]);
       
        return  redirect()->route('designations.index')->with('success', 'Designation Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function show(Designation $designation)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function edit(Designation $designation)
    {
        return view('designations.edit', [ 'designation' => $designation ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Designation $designation)
    {
        $request->validate([
            'name' => 'required|max:100|'.Rule::unique('designations')->ignore($designation)
        ]);
       
        $designation->update(['name' => $request->name]);

        return  redirect()->route('designations.index')->with('success', 'Designation Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Designation $designation)
    {
        $designation->delete();
        return back()->with('success', 'Designation Deleted');
    }
}
