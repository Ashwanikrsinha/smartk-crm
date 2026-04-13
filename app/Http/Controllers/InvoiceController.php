<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Visit;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\InvoiceRequest;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Invoice::class, 'invoice');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $invoices = Invoice::with([
                'customer' => function($query){ $query->select('id', 'name'); },
                'visit' => function ($query) { $query->select('id', 'visit_number'); }
                ])
                ->select('id', 'invoice_number', 'invoice_date', 'customer_id', 'status', 'amount', 'visit_id');
  
            return DataTables::of($invoices)
                    ->editColumn('invoice_number', function ($invoice) {
                        return "INV-{$invoice->invoice_number}";
                    })
                    ->editColumn('invoice_date', function ($invoice) {
                        return $invoice->invoice_date->format('d M, Y');
                    })
                    ->editColumn('status', function ($invoice) {
                        return isset($invoice->status) ? ucwords($invoice->status) : '';
                    })
                    ->editColumn('visit.visit_number', function ($invoice) {
                        return 'V-'.$invoice->visit->visit_number;
                    })
                    ->addColumn('action', function ($invoice) {
                        return view('invoices.buttons')->with(['invoice' => $invoice]);
                    })->make(true);
        }

        return view('invoices.index');
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
                    ->doesntHave('invoice')
                    ->pluck('visit_number', 'id');
            
           if($visits->isEmpty()){
            return redirect()->route('invoices.create')->withErrors(['message' => 'No visits found'])->withInput();
           }
        }

        if($request->filled('customer_id') && $request->filled('visit_id')){

            $visit = Visit::select('id', 'visit_number' ,'customer_id')->findOrFail($request->visit_id);

            if($visit->invoice()->count() > 0){
                return back()->withErrors(['message' => 'Visit is already in use']);
            }

            $visit->load(['customer' => function($query){$query->select('id', 'name'); } ]);

            if($visit->visitItems()->count() > 0){
                $visit->load(['visitItems.product', 'visitItems.unit']);
            }
            
        } 

        $products = Product::orderBy('name')->pluck('name', 'id');
        $units = Unit::orderBy('name')->pluck('name', 'id');
        $customers = Customer::orderBy('name')->pluck('name', 'id');
        $invoice_number = Invoice::invoiceNumber();
        $statuses = Invoice::status();
        $follow_types = Invoice::followTypes();


        return view(
            'invoices.create',
            compact(
                'products',
                'units',
                'customers',
                'invoice_number',
                'statuses',
                'follow_types',
                'visits',
                'visit'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceRequest $request)
    {
      

        DB::transaction(function () use ($request) {

            $invoice = Invoice::create([
                'invoice_number' =>  Invoice::invoiceNumber(),
                'invoice_date' => $request->invoice_date,
                'customer_id' => $request->customer_id,
                'visit_id' => $request->visit_id,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'status' => $request->status,
                'reason' => $request->reason,
                'amount' => $request->amount,
                'follow_type' => $request->follow_type,
                'follow_date' => $request->follow_date,
                'remarks' => $request->remarks,
                'terms' => $request->terms,
            ]);

            $invoice->createInvoiceItems($request);
        

            if ($request->filled('attachments.0')) {
                $invoice->createInvoiceAttachments($request);
            }
            
        });
            
        return  redirect()->route('invoices.index')->with('success', 'Performa Invoice Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Invoice $invoice)
    {


        $invoice->load(['invoiceItems.product', 'invoiceItems.unit']);

        $amount_in_words = strtoupper((new \NumberFormatter('en',\NumberFormatter::SPELLOUT))->format($invoice->total_amount));


        return view('invoices.print', compact('invoice', 'amount_in_words'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Invoice $invoice)
    {

        $visits = $visit = null;

        if ($request->filled('customer_id')) {

                $visits = Visit::where('customer_id', $request->customer_id)
                    ->doesntHave('invoice')
                    ->pluck('visit_number', 'id');
            
           if($visits->isEmpty()){
            return redirect()
                ->route('invoices.edit', ['invoice' => $invoice])
                ->withErrors(['message' => 'No visits found'])->withInput();
           }
        }

        if($request->filled('customer_id') && $request->filled('visit_id')){

            $visit = Visit::select('id', 'visit_number' ,'customer_id')->findOrFail($request->visit_id);

            if($visit->invoice()->count() > 0){
                return back()->withErrors(['message' => 'Visit is already in use']);
            }

            $visit->load(['customer' => function($query){$query->select('id', 'name'); } ]);

            if($visit->visitItems()->count() > 0){
                $visit->load(['visitItems.product', 'visitItems.unit']);
            }
            
        } 
       
        $invoice->load(['invoiceItems.product', 'invoiceItems.unit']);
    
        $products = Product::orderBy('name')->pluck('name', 'id');
        $units = Unit::orderBy('name')->pluck('name', 'id');
        $customers = Customer::orderBy('name')->pluck('name', 'id');
        $statuses = Invoice::status();
        $follow_types = Invoice::followTypes();

        return view(
            'invoices.edit',
            compact(
                'products',
                'units',
                'customers',
                'invoice',
                'statuses',
                'follow_types',
                'visit',
                'visits'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        
        DB::transaction(function () use ($request, $invoice) {

            $invoice->update([
                'invoice_date' => $request->invoice_date,
                'customer_id' => $request->customer_id,
                'visit_id' => $request->visit_id,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'status' => $request->status,
                'reason' => $request->reason,
                'follow_type' => $request->follow_type,
                'follow_date' => $request->follow_date,
                'remarks' => $request->remarks,
                'terms' => $request->terms,
                'amount' => $request->amount,
            ]);

            $invoice->invoiceItems()->delete();
            $invoice->createInvoiceItems($request);
        

            if ($request->filled('attachments.0')) {
                $invoice->createInvoiceAttachments($request);
            }
            
        });
        

        return  back()->with('success', 'Performa Invoice Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {

       if($invoice->attachments()->count() > 0){

            foreach ($invoice->attachments as $attachment) {
                Storage::delete($attachment);
            }

       }

        $invoice->attachments()->delete();
        $invoice->invoiceItems()->delete();
        $invoice->delete();
        return back()->with('success', 'Performa Invoice Deleted');
    }
}
