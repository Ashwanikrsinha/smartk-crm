<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Visit;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\QuotationRequest;
use Illuminate\Support\Str;

class QuotationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Quotation::class, 'quotation');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $quotations = Quotation::with([
                'customer' => function ($query) {
                    $query->select('id', 'name');
                },
                'user' => function ($query) {
                    $query->select('id', 'username');
                },
                'visit' => function ($query) {
                    $query->select('id', 'visit_number');
                }
                ])
                ->select();
  
            return DataTables::of($quotations)
                    ->editColumn('quotation_number', function ($quotation) {
                        return "Q-{$quotation->quotation_number}";
                    })
                    ->editColumn('quotation_date', function ($quotation) {
                        return $quotation->quotation_date->format('d M, Y');
                    })
                    ->editColumn('visit.visit_number', function ($quotation) {
                        return 'V-'.$quotation->visit->visit_number;
                    })
                    ->editColumn('status', function ($quotation) {
                        return isset($quotation->status) ? ucwords($quotation->status) : '';
                    })
                    ->addColumn('action', function ($quotation) {
                        return view('quotations.buttons')->with(['quotation' => $quotation]);
                    })->make(true);
        }

        return view('quotations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $visits = $visit = null;

        if ($request->filled('customer_id')) {

                $visits = Visit::where('customer_id', $request->customer_id)
                    ->doesntHave('quotation')
                    ->pluck('visit_number', 'id');
            
           if($visits->isEmpty()){
            return redirect()->route('quotations.create')->withErrors(['message' => 'No visits found'])->withInput();
           }
        }

        if($request->filled('customer_id') && $request->filled('visit_id')){

            $visit = Visit::select('id', 'visit_number' ,'customer_id')->findOrFail($request->visit_id);

            if($visit->quotation()->count() > 0){
                return back()->withErrors(['message' => 'Visit is already in use']);
            }

            $visit->load(['customer' => function($query){$query->select('id', 'name'); } ]);

            if($visit->visitItems()->count() > 0){
                $visit->load(['visitItems.product', 'visitItems.unit']);
            }
        } 

        $customers = Customer::orderBy('name')->pluck('name', 'id');
        $products = Product::orderBy('name')->pluck('name', 'id');
        $units = Unit::orderBy('name')->pluck('name', 'id');
        $users = User::active()->orderBy('username')->pluck('username', 'id');
        $statuses = Quotation::status();
        $follow_types = Quotation::followTypes();

        return view(
            'quotations.create',
            compact(
                'products',
                'units',
                'users',
                'customers',
                'statuses',
                'follow_types',
                'visits',
                'visit',
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(QuotationRequest $request)
    {


        DB::transaction(function () use ($request) {

            $visit = Visit::select('id', 'customer_id')->findOrFail($request->visit_id);

            $quotation = Quotation::create([
                'quotation_number' =>  Quotation::quotationNumber(),
                'quotation_date' => $request->quotation_date,
                'customer_id' => $visit->customer_id,
                'user_id' => $request->user_id,
                'visit_id' => $request->visit_id,
                'status' => $request->status,
                'reason' => $request->reason,
                'follow_type' => $request->follow_type,
                'follow_date' => $request->follow_date,
                'remarks' => $request->remarks,
            ]);

            if ($request->has('products')) {
                $quotation->createQuotationItems($request);
            }

            if ($request->filled('attachments.0')) {
                $quotation->createQuotationAttachments($request);
            }
            
        });
            
        return  redirect()->route('quotations.index')->with('success', 'Quotation Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quotation $quotation
     * @return \Illuminate\Http\Response
     */
    public function show(Quotation $quotation)
    {
        $quotation->load(['visit' => function($query){ $query->select('id', 'visit_number'); } ]);

        if($quotation->quotationItems->count()){
            $quotation->load(['quotationItems.product', 'quotationItems.unit']);
        }

        return view('quotations.show', compact('quotation'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Quotation $quotation
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Quotation $quotation)
    {
       
        $quotation->load(['quotationItems.product', 'quotationItems.unit']);

        $visits = $visit = null;

        if ($request->filled('customer_id')) {

           $visits = Visit::where('customer_id', $request->customer_id)
            ->doesntHave('quotation')
            ->pluck('visit_number', 'id');

           if($visits->isEmpty()){
                return redirect()
                ->route('quotations.edit', ['quotation' => $quotation])
                ->withErrors(['message' => 'No visits found'])->withInput();
           }

        }

        if($request->filled('customer_id') && $request->filled('visit_id')){

            $visit = Visit::select('id', 'visit_number' ,'customer_id')->findOrFail($request->visit_id);

            if($visit->quotation()->count() > 0){
                return back()->withErrors(['message' => 'Visit is already in use']);
            }

            $visit->load(['customer' => function($query){$query->select('id', 'name'); } ]);

            if($visit->visitItems()->count() > 0){
                $visit->load(['visitItems.product', 'visitItems.unit']);
            }
        } 

        $customers = Customer::orderBy('name')->pluck('name', 'id');
        $products = Product::orderBy('name')->pluck('name', 'id');
        $units = Unit::orderBy('name')->pluck('name', 'id');
        $users = User::active()->orderBy('username')->pluck('username', 'id');
        $statuses = Quotation::status();
        $follow_types = Quotation::followTypes();
        
        return view(
            'quotations.edit',
            compact(
                'quotation',
                'customers',
                'products',
                'units',
                'statuses',
                'follow_types',
                'users',
                'visits',
                'visit'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Quotation $quotation
     * @return \Illuminate\Http\Response
     */
    public function update(QuotationRequest $request, Quotation $quotation)
    {
        $request->validated();

        DB::transaction(function () use ($request, $quotation) {
           
            $visit = Visit::select('id', 'customer_id')->findOrFail($request->visit_id);

            $quotation->update([
                'quotation_date' => $request->quotation_date,
                'customer_id' => $visit->customer_id,
                'user_id' => $request->user_id,
                'visit_id' => $request->visit_id,
                'status' => $request->status,
                'reason' => $request->reason,
                'follow_type' => $request->follow_type,
                'follow_date' => $request->follow_date,
                'remarks' => $request->remarks,
            ]);


           if ($request->has('products')) {
               $quotation->quotationItems()->delete();
                $quotation->createQuotationItems($request);
            }

            if ($request->filled('attachments.0')) {
                $quotation->createQuotationAttachments($request);
            }

        });

        return  back()->with('success', 'Quotation Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Quotation $quotation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quotation $quotation)
    {
        $quotation->quotationItems()->delete();
        $quotation->quotationAttachments()->delete();
        $quotation->delete();
        return back()->with('success', 'Quotation Deleted');
    }
}
