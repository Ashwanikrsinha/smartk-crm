<?php

namespace App\Http\Controllers;

use App\Models\CustomerType;
use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CustomerTypeController extends Controller
{
    public function __construct()
    {
       $this->authorizeResource(CustomerType::class, 'type');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $types = CustomerType::select();
  
            return DataTables::of($types)
                  ->addColumn('action', function ($type) {
                      return view('customer-types.buttons')->with(['type' => $type]);
                  })->make(true);
        }

        return view('customer-types.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customer-types.create');
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
            'name' => 'required:max:50|unique:customer_types'
        ]);
       
        CustomerType::create(['name' => $request->name]);

        return  redirect()->route('customer-types.index')->with('success', 'Customer Type Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerType  $type
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerType $type)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerType  $type
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerType $type)
    {
        return view('customer-types.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerType  $type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerType $type)
    {
        $request->validate([
            'name' => 'required|max:50',
        ]);
       
        $type->update(['name' => $request->name]);

        return  redirect()->route('customer-types.index')->with('success', 'Customer Type Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerType  $type
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerType $type)
    {
        $type->delete();
        return back()->with('success', 'Customer Type Deleted');
    }
}
