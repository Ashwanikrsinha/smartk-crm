<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Transport;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\BillRequest;
use Illuminate\Support\Str;

class BillController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Bill::class, 'bill');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if($request->type !== Bill::SALE && $request->type !== Bill::PURCHASE){
            return abort(404);
        }

        if ($request->ajax()) {

            $bills = Bill::with(['customer' => function($query){ $query->select('id', 'name'); } ])
                ->where('type', $request->type)
                ->select('id', 'bill_number', 'bill_date', 'customer_id', 'type', 'is_approved', 'total_amount');
  
            return DataTables::of($bills)
                    ->editColumn('bill_number', function ($bill) {
                        return "BILL-{$bill->bill_number}";
                    })
                    ->editColumn('bill_date', function ($bill) {
                        return $bill->bill_date->format('d M, Y');
                    })
                    ->addColumn('type', function ($bill) {
                        return ucwords($bill->type);
                    })
                    ->addColumn('status', function ($bill) {
                        return $bill->is_approved ? 'Approved': 'Not Approved';
                    })
                    ->addColumn('action', function ($bill) {
                        return view('bills.buttons')->with(['bill' => $bill]);
                    })->make(true);
        }

        return view('bills.index', ['type' => $request->type]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        if($request->type !== Bill::SALE && $request->type !== Bill::PURCHASE){
            return abort(404);
        }

        $products = Product::orderBy('name')->pluck('name', 'id');
        $units = Unit::orderBy('name')->pluck('name', 'id');
        $customers = Customer::orderBy('name')->pluck('name', 'id');
        $transports = Transport::orderBy('name')->pluck('name', 'id');
        $bill_number = Bill::billNumber();
        $type = $request->type;

        return view(
            'bills.create',
            compact(
                'products',
                'units',
                'customers',
                'transports',
                'bill_number',
                'type'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BillRequest $request)
    {
      
        DB::transaction(function () use ($request) {

            $bill = Bill::create([
                'bill_number' =>  Bill::billNumber(),
                'bill_date' => $request->bill_date,
                'type' => $request->type,
                'is_approved' => $request->has('is_approved'),
                'customer_id' => $request->customer_id,
                'phone_number' => $request->phone_number,
                'gst_number' => $request->gst_number,
                'address' => $request->address,
                'transport_id' => $request->transport_id,
                'vehicle_number' => $request->vehicle_number,
                'bilty_number' => $request->bilty_number,
                'delivery_date' => $request->delivery_date,
                'remarks' => $request->remarks,
                'terms' => $request->terms,
                'amount' => $request->amount,
                'cgst_amount' => $request->cgst_amount,
                'sgst_amount' => $request->sgst_amount,
                'igst_amount' => $request->igst_amount,
                'transport_charges' => $request->transport_charges,
                'extra_charges' => $request->extra_charges,
                'total_amount' => $request->total_amount
            ]);

            $bill->createBillItems($request);
        

            if ($request->filled('attachments.0')) {
                $bill->createBillAttachments($request);
            }
            
        });
            
        return redirect()
                ->route('bills.index', ['type' => $request->type])
                ->with('success', ucfirst($request->type).' Bill Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bill $bill
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Bill $bill)
    {

        $type = $request->type;

        if($type !== Bill::WITH_PRICE && $type !== Bill::WITHOUT_PRICE){
            return abort(404);
        }


        $bill->load(['billItems.product', 'billItems.unit']);

        $amount_in_words = strtoupper((new \NumberFormatter('en',\NumberFormatter::SPELLOUT))->format($bill->total_amount));


        return view('bills.print', compact('bill', 'type', 'amount_in_words'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bill $bill
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Bill $bill)
    {
       
        $bill->load(['billItems.product', 'billItems.unit']);
    
        $products = Product::orderBy('name')->pluck('name', 'id');
        $units = Unit::orderBy('name')->pluck('name', 'id');
        $customers = Customer::orderBy('name')->pluck('name', 'id');
        $transports = Transport::orderBy('name')->pluck('name', 'id');
        $type = $request->type;

        return view(
            'bills.edit',
            compact(
                'products',
                'units',
                'customers',
                'transports',
                'bill'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bill $bill
     * @return \Illuminate\Http\Response
     */
    public function update(BillRequest $request, Bill $bill)
    {
        
        DB::transaction(function () use ($request, $bill) {

            $bill->update([
                'bill_date' => $request->bill_date,
                'is_approved' => $request->has('is_approved'),
                'customer_id' => $request->customer_id,
                'phone_number' => $request->phone_number,
                'gst_number' => $request->gst_number,
                'address' => $request->address,
                'transport_id' => $request->transport_id,
                'vehicle_number' => $request->vehicle_number,
                'bilty_number' => $request->bilty_number,
                'delivery_date' => $request->delivery_date,
                'remarks' => $request->remarks,
                'terms' => $request->terms,
                'amount' => $request->amount,
                'cgst_amount' => $request->cgst_amount,
                'sgst_amount' => $request->sgst_amount,
                'igst_amount' => $request->igst_amount,
                'transport_charges' => $request->transport_charges,
                'extra_charges' => $request->extra_charges,
                'total_amount' => $request->total_amount
            ]);

            $bill->billItems()->delete();
            $bill->createBillItems($request);
        

            if ($request->filled('attachments.0')) {
                $bill->createBillAttachments($request);
            }
            
        });
        

        return  back()->with('success', ucfirst($request->type).' Bill Updated');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bill $bill
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bill $bill)
    {

       if($bill->attachments()->count() > 0){

            foreach ($bill->attachments as $attachment) {
                Storage::delete($attachment);
            }

       }

        $bill->attachments()->delete();
        $bill->billItems()->delete();
        $bill->delete();
        return back()->with('success', 'Bill Deleted');
    }
}
