<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierType;
use App\Models\Segment;
use App\Models\State;
use App\Models\User;
use App\Models\Designation;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\SupplierRequest;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Supplier::class, 'supplier');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        if ($request->ajax() && $request->filled('supplier_id')) {

            return Supplier::select('id', 'name', 'address', 'phone_number')->findOrFail($request->supplier_id);
            
        }

        if ($request->ajax()) {
            
            $suppliers = Supplier::with(['segment'])->select();
  
            return DataTables::of($suppliers)
                  ->addColumn('action', function ($supplier) {
                      return view('suppliers.buttons')->with(['supplier' => $supplier]);
                  })
                  ->make(true);
        }

        return view('suppliers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $supplier_types = SupplierType::orderBy('name')->pluck('name', 'id');
        $segments = Segment::orderBy('name')->pluck('name', 'id');
        $designations = Designation::orderBy('name')->pluck('name', 'id');
        $states = State::orderBy('name')->pluck('name');
        
        return view('suppliers.create', compact('segments', 'supplier_types', 'designations', 'states'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierRequest $request)
    {
        DB::transaction(function () use ($request) {

            $supplier = Supplier::create([
            'name' => $request->name,
            'segment_id' => $request->segment_id,
            'supplier_types' => implode(',', $request->supplier_types),
            'city' => $request->city,
            'state' => $request->state,
            'address' => $request->address,
            'email' => $request->email,
            'description' => $request->description,
            'phone_number' => $request->phone_number,
            'gst_number' => $request->gst_number,
           ]);


            if ($request->filled('person_name.0')) {
                $supplier->createContacts($request);
            }
        });
            

        return  redirect()->route('suppliers.index')->with('success', 'Supplier Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        $supplier->load('contacts.designation');
        $visits = null;

        return view('suppliers.show', compact('supplier', 'visits'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        $supplier->load('contacts');
        $supplier_types = SupplierType::orderBy('name')->pluck('name', 'id');
        $segments = Segment::orderBy('name')->pluck('name', 'id');
        $designations = Designation::orderBy('name')->pluck('name', 'id');
        $states = State::orderBy('name')->pluck('name');
        

        return view('suppliers.edit', compact('segments', 'supplier_types', 'supplier', 'designations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(SupplierRequest $request, Supplier $supplier)
    {
        DB::transaction(function () use ($request, $supplier) {

            $supplier->update([
                'name' => $request->name,
                'segment_id' => $request->segment_id,
                'supplier_types' => implode(',', $request->supplier_types),
                'city' => $request->city,
                'state' => $request->state,
                'address' => $request->address,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'description' => $request->description,
                'gst_number' => $request->gst_number,
            ]);
            
            if ($supplier->contacts->count() > 0) {
                $supplier->contacts()->delete();
                $supplier->createContacts($request);
            } elseif ($request->filled('person_name.0')) {
                $supplier->createContacts($request);
            }
        });

        return  back()->with('success', 'Supplier Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->contacts()->delete();
        $supplier->delete();
        return back()->with('success', 'Supplier Deleted');
    }
}
