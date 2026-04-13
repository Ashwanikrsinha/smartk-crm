<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Segment;
use App\Models\State;
use App\Models\Designation;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\CustomerRequest;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Customer::class, 'customer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        if ($request->ajax() && $request->filled('customer_id')) {

            return Customer::findOrFail($request->customer_id);
            
        }

        if ($request->ajax()) {
            
            $customers = Customer::with(['segment'])->select();
  
            return DataTables::of($customers)
                  ->addColumn('action', function ($customer) {
                      return view('customers.buttons')->with(['customer' => $customer]);
                  })
                  ->make(true);
        }

        return view('customers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customer_types = CustomerType::orderBy('name')->pluck('name', 'id');
        $segments = Segment::orderBy('name')->pluck('name', 'id');
        $designations = Designation::orderBy('name')->pluck('name', 'id');
        $states = State::orderBy('name')->pluck('name');

        return view('customers.create', compact('segments', 'customer_types', 'designations', 'states'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {

        if($request->ajax()){

            $new_customer = Customer::create($request->only('name', 'phone_number', 'email'));

            $customers = Customer::orderBy('name')->pluck('name', 'id');
            return view('customers.option', compact('customers', 'new_customer'));

        }

        DB::transaction(function () use ($request) {

            $customer = Customer::create([
            'name' => $request->name,
            'segment_id' => $request->segment_id,
            'customer_types' => isset($request->customer_types) ? implode(',', $request->customer_types) : null,
            'city' => $request->city,
            'state' => $request->state,
            'address' => $request->address,
            'email' => $request->email,
            'description' => $request->description,
            'phone_number' => $request->phone_number,
            'gst_number' => $request->gst_number,
           ]);

            if ($request->filled('person_name.0')) {
                $customer->createContacts($request);
            }
        });
            

        return  redirect()->route('customers.index')->with('success', 'Customer Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        $customer->load('contacts.designation');
        $visits = $customer->visits()
                    ->with('user', 'purpose')
                    ->paginate(50)
                    ->fragment('visits');

        return view('customers.show', compact('customer', 'visits'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        $customer->load('contacts');
        $customer_types = CustomerType::orderBy('name')->pluck('name', 'id');
        $segments = Segment::orderBy('name')->pluck('name', 'id');
        $designations = Designation::orderBy('name')->pluck('name', 'id');
        $states = State::orderBy('name')->pluck('name');

        return view('customers.edit', compact('segments', 'customer_types', 'customer', 'designations', 'states'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        DB::transaction(function () use ($request, $customer) {

            $customer->update([
                'name' => $request->name,
                'segment_id' => $request->segment_id,
                'customer_types' => isset($request->customer_types) ? implode(',', $request->customer_types) : null,
                'city' => $request->city,
                'state' => $request->state,
                'address' => $request->address,
                'email' => $request->email,
                'description' => $request->description,
                'phone_number' => $request->phone_number,
                'gst_number' => $request->gst_number,
               ]);
    
            if ($customer->contacts->count() > 0) {
                $customer->contacts()->delete();
                $customer->createContacts($request);
            } elseif ($request->filled('person_name.0')) {
                $customer->createContacts($request);
            }
        });

        return  back()->with('success', 'Customer Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->contacts()->delete();
        $customer->delete();

        return back()->with('success', 'Customer Deleted');
    }
}
