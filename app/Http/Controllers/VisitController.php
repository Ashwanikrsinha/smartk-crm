<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Purpose;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\VisitRequest;
use Illuminate\Support\Str;

class VisitController extends Controller
{

    public function __construct()
    {
       $this->authorizeResource(Visit::class, 'visit');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $visits = Visit::with([
                'customer' => function ($query) { $query->select('id', 'name', 'segment_id'); }, 
                'user' => function ($query) { $query->select('id', 'username'); },
                'purpose'
                ])
                ->select();
  
            return DataTables::of($visits)
                    ->editColumn('visit_number', function ($visit) {
                        return "V-{$visit->visit_number}";
                    })
                    ->editColumn('visit_date', function ($visit) {
                        return $visit->visit_date->format('d M, Y');
                    })
                    ->editColumn('customer.name', function ($visit) {
                        return Str::limit($visit->customer->name, 20);
                    })
                    ->editColumn('level', function ($visit) {
                        return isset($visit->level) ? ucwords($visit->level) : '';
                    })
                    ->editColumn('status', function ($visit) {
                        return isset($visit->status) ? ucwords($visit->status) : '';
                    })
                  ->addColumn('action', function ($visit) {
                      return view('visits.buttons')->with(['visit' => $visit]);
                  })->make(true);
        }

        return view('visits.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->pluck('name', 'id');
        $purposes = Purpose::orderBy('name')->pluck('name', 'id');
        $follow_types = Visit::followTypes();
        $statuses = Visit::status();
        $levels = Visit::levels();
        $products = Product::orderBy('name')->pluck('name', 'id');
        $units = Unit::orderBy('name')->pluck('name', 'id');
        $users = User::active()->orderBy('username')->pluck('username', 'id');

        return view('visits.create', 
            compact('customers', 'purposes', 'users', 'follow_types', 'statuses', 'levels', 'products', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VisitRequest $request)
    {
       
        DB::transaction(function () use ($request) {

            $visit = Visit::create([
                'visit_date' => $request->visit_date,
                'follow_date' => $request->follow_date,
                'visit_number' =>  Visit::visitNumber(), 
                'user_id' => $request->user_id,
                'purpose_id' => $request->purpose_id,
                'customer_id' => $request->customer_id,
                'level' => $request->level,
                'status' => $request->status,
                'order_amount' => $request->order_amount,
                'insight' => $request->insight,
                'action' => $request->action,
                'reason' => $request->reason,
                'follow_type' => $request->follow_type,
                'follow_date' => $request->follow_date,
                'description' => $request->description,
            ]);

           if ($request->has('products')) {
                $visit->createVisitItems($request);
            }

           if ($request->filled('attachments.0')) {
               $visit->createVisitAttachments($request);
            }
        
        });
            
        return  redirect()->route('visits.index')->with('success', 'Visit Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Visit $visit
     * @return \Illuminate\Http\Response
     */
    public function show(Visit $visit)
    {
        if($visit->visitItems->count()){
            $visit->load(['visitItems.product', 'visitItems.unit']);
        }
        
        if($visit->attachments->count() > 0){
            $visit->load('attachments');
        }

        return view('visits.show', compact('visit'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Visit $visit
     * @return \Illuminate\Http\Response
     */
    public function edit(Visit $visit)
    {
        if($visit->attachments->count() > 0){
            $visit->load('attachments');
        }

        $customers = Customer::orderBy('name')->pluck('name', 'id');
        $purposes = Purpose::orderBy('name')->pluck('name', 'id');
        $follow_types = Visit::followTypes();
        $statuses = Visit::status();
        $levels = Visit::levels();
        $users = User::active()->orderBy('username')->pluck('username', 'id');
        $products = Product::orderBy('name')->pluck('name', 'id');
        $units = Unit::orderBy('name')->pluck('name', 'id');

        return view('visits.edit', 
        compact('visit','customers', 'purposes', 'users', 'statuses', 'follow_types', 'levels', 'products', 'units'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Visit $visit
     * @return \Illuminate\Http\Response
     */
    public function update(VisitRequest $request, Visit $visit)
    {
        
        DB::transaction(function () use ($request, $visit) {

            $visit->update([
                'visit_date' => $request->visit_date,
                'follow_date' => $request->follow_date,
                'user_id' => $request->user_id,
                'purpose_id' => $request->purpose_id,
                'customer_id' => $request->customer_id,
                'order_amount' =>$request->order_amount,
                'insight' => $request->insight,
                'action' => $request->action,
                'reason' => $request->reason,
                'follow_type' => $request->follow_type,
                'follow_date' => $request->follow_date,
                'level' => $request->level,
                'status' => $request->status,
                'description' => $request->description,
           ]);

           if ($request->has('products')) {
            $visit->visitItems()->delete();
            $visit->createVisitItems($request);
           }

           if ($request->filled('attachments.0')) {
               $visit->createVisitAttachments($request);
           }
        
        });
            
        return  back()->with('success', 'Visit Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Visit $visit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Visit $visit)
    {
        $visit->visitItems()->delete();
        $visit->attachments()->delete();
        $visit->delete();
        return back()->with('success', 'Visit Deleted');
    }
}
