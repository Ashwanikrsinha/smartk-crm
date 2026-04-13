<?php

namespace App\Http\Controllers;

use App\Models\SupplierType;
use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SupplierTypeController extends Controller
{
    public function __construct()
    {
       $this->authorizeResource(SupplierType::class, 'type');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $types = SupplierType::select();
  
            return DataTables::of($types)
                  ->addColumn('action', function ($type) {
                      return view('supplier-types.buttons')->with(['type' => $type]);
                  })->make(true);
        }

        return view('supplier-types.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('supplier-types.create');
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
            'name' => 'required:max:50|unique:supplier_types'
        ]);
       
        SupplierType::create(['name' => $request->name]);

        return  redirect()->route('supplier-types.index')->with('success', 'Supplier Type Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SupplierType  $type
     * @return \Illuminate\Http\Response
     */
    public function show(SupplierType $type)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SupplierType  $type
     * @return \Illuminate\Http\Response
     */
    public function edit(SupplierType $type)
    {
        return view('supplier-types.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SupplierType  $type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SupplierType $type)
    {
        $request->validate([
            'name' => 'required|max:50',
        ]);
       
        $type->update(['name' => $request->name]);

        return  redirect()->route('supplier-types.index')->with('success', 'Supplier Type Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SupplierType  $type
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupplierType $type)
    {
        $type->delete();
        return back()->with('success', 'Supplier Type Deleted');
    }
}
