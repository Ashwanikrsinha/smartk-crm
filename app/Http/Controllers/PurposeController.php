<?php

namespace App\Http\Controllers;

use App\Models\Purpose;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class PurposeController extends Controller
{

    public function __construct()
    {
       $this->authorizeResource(Purpose::class, 'purpose');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $purposes = Purpose::select();
  
            return DataTables::of($purposes)
                  ->addColumn('action', function ($purpose) {
                      return view('purposes.buttons')->with(['purpose' => $purpose]);
                  })->make(true);
        }

        return view('purposes.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('purposes.create');
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
            'name' => 'required|unique:purposes|max:100',
        ]);

        Purpose::create(['name' => $request->name]);
       
        return  redirect()->route('purposes.index')->with('success', 'Purpose Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purpose  $purpose
     * @return \Illuminate\Http\Response
     */
    public function show(Purpose $purpose)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Purpose  $purpose
     * @return \Illuminate\Http\Response
     */
    public function edit(Purpose $purpose)
    {
        return view('purposes.edit', [ 'purpose' => $purpose ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Purpose  $purpose
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Purpose $purpose)
    {
        $request->validate([
            'name' => 'required|max:100|'.Rule::unique('purposes')->ignore($purpose)
        ]);
       
        $purpose->update(['name' => $request->name]);

        return  redirect()->route('purposes.index')->with('success', 'Purpose Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Purpose  $purpose
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purpose $purpose)
    {
        $purpose->delete();
        return back()->with('success', 'Purpose Deleted');
    }
}
