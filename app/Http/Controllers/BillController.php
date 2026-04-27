<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillingEntry;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Transport;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Accounts + Admin only — SM has NO billing access
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAccounts() && !auth()->user()->isAdmin()) {
                abort(403, 'Only Accounts team and Administrators can manage bills.');
            }
            return $next($request);
        })->except(['index', 'show']); // index/show also shown to SM (read-only)
    }

    // -------------------------------------------------------
    // INDEX
    // -------------------------------------------------------

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $teamIds = auth()->user()->teamMemberIds();

            $bills = Bill::with([
                'customer:id,name,school_code',
                'invoice:id,po_number',
            ])
                ->where('type', Bill::SALE)
                ->whereIn('customer_id', function ($q) use ($teamIds) {
                    $q->select('customer_id')
                        ->from('invoices')
                        ->whereIn('user_id', $teamIds);
                })
                ->select(
                    'id',
                    'bill_number',
                    'bill_date',
                    'customer_id',
                    'type',
                    'total_amount',
                    'is_approved'
                );

            return DataTables::of($bills)
                ->editColumn('bill_number',  fn($b) => 'BILL-' . str_pad($b->bill_number, 4, '0', STR_PAD_LEFT))
                ->editColumn('bill_date',    fn($b) => $b->bill_date->format('d M, Y'))
                ->editColumn('total_amount', fn($b) => '₹' . number_format($b->total_amount, 2))
                ->editColumn('is_approved',  fn($b) => $b->is_approved
                    ? '<span class="badge bg-success">Verified</span>'
                    : '<span class="badge bg-warning text-dark">Pending</span>')
                ->addColumn('po_number', fn($b) => $b->invoice?->po_number ?? '—')
                ->addColumn('action',    fn($b) => view('bills.buttons', ['bill' => $b])->render())
                ->rawColumns(['is_approved', 'action'])
                ->make(true);
        }
        $type = $request->type ?? Bill::SALE;
        return view('bills.index', compact('type'));
    }
    public function create(Request $request)
    {
        $invoice = null;

        if ($request->filled('invoice_id')) {
            $invoice = Invoice::with([
                'customer',
                'invoiceItems.product',
                'invoiceItems.unit',
            ])
                ->where('status', Invoice::STATUS_APPROVED)
                ->findOrFail($request->invoice_id);
        }

        $approvedPos = Invoice::with('customer:id,name')
            ->where('status', Invoice::STATUS_APPROVED)
            ->whereIn('user_id', auth()->user()->teamMemberIds())
            ->orderByDesc('invoice_date')
            ->get(['id', 'po_number', 'customer_id', 'amount']);


        $products = Product::orderBy('name')->pluck('name', 'id');
        $units = Unit::orderBy('name')->pluck('name', 'id');
        $customers = Customer::orderBy('name')->pluck('name', 'id');
        $transports = Transport::orderBy('name')->pluck('name', 'id');
        $bill_number = Bill::billNumber();
        $type       = $request->type ?? Bill::SALE;
        return view('bills.create', compact('invoice', 'approvedPos', 'units', 'products', 'bill_number', 'type','customers','transports'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'   => 'required|exists:invoices,id',
            'bill_date'    => 'required|date',
            'address'      => 'nullable|string|max:500',
            'phone_number' => 'nullable|string|max:15',
            'gst_number'   => 'nullable|string|max:15',
            'terms'        => 'nullable|string|max:2000',
            'remarks'      => 'nullable|string|max:1000',
            'products'     => 'required|array|min:1',
            'products.*'   => 'required|exists:products,id',
            'quantities.*' => 'required|numeric|min:1',
            'rates.*'      => 'required|numeric|min:0',
            'amounts.*'    => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {

            $invoice  = Invoice::findOrFail($request->invoice_id);
            $subtotal = (float) array_sum($request->amounts);
            $cgst     = (float) ($request->cgst_amount      ?? 0);
            $sgst     = (float) ($request->sgst_amount      ?? 0);
            $igst     = (float) ($request->igst_amount      ?? 0);
            $transport = (float) ($request->transport_charges ?? 0);
            $extra    = (float) ($request->extra_charges     ?? 0);
            $total    = $subtotal + $cgst + $sgst + $igst + $transport + $extra;

            $bill = Bill::create([
                'bill_number'       => Bill::billNumber(),
                'type'              => Bill::SALE,
                'bill_date'         => $request->bill_date,
                'customer_id'       => $invoice->customer_id,
                'invoice_id'        => $invoice->id,
                'phone_number'      => $request->phone_number ?? $invoice->phone_number,
                'gst_number'        => $request->gst_number   ?? $invoice->customer->gstin,
                'address'           => $request->address       ?? $invoice->address,
                'terms'             => $request->terms,
                'remarks'           => $request->remarks,
                'amount'            => $subtotal,
                'cgst_amount'       => $cgst,
                'sgst_amount'       => $sgst,
                'igst_amount'       => $igst,
                'transport_charges' => $transport,
                'extra_charges'     => $extra,
                'total_amount'      => $total,
                'is_approved'       => 0,
            ]);

            foreach ($request->products as $i => $productId) {
                $bill->items()->create([
                    'product_id'  => $productId,
                    'description' => $request->descriptions[$i] ?? '',
                    'unit_id'     => $request->units[$i],
                    'quantity'    => $request->quantities[$i],
                    'rate'        => $request->rates[$i],
                    'amount'      => $request->amounts[$i],
                ]);
            }

            // Create a billing_entry record (source='crm') so recalculateBilling()
            // picks it up automatically via the BillingEntry::booted() observer
            BillingEntry::create([
                'invoice_id'       => $invoice->id,
                'billed_amount'    => $total,
                'source'           => BillingEntry::SOURCE_CRM,
                'reference_number' => 'BILL-' . str_pad($bill->bill_number, 4, '0', STR_PAD_LEFT),
                'remarks'          => "CRM bill generated",
                'entered_by'       => auth()->id(),
                'billed_at'        => $request->bill_date,
            ]);
            // BillingEntry::booted() saved() fires → invoice->recalculateBilling()
        });

        return redirect()->route('bills.index')
            ->with('success', 'Sales bill created and billing amount updated.');
    }
    public function show(Bill $bill)
    {
        $bill->load(['customer', 'items.product', 'items.unit', 'invoice']);

        $amountInWords = strtoupper(
            (new \NumberFormatter('en', \NumberFormatter::SPELLOUT))
                ->format($bill->total_amount)
        ) . ' RUPEES ONLY';

        return view('bills.show', compact('bill', 'amountInWords'));
    }
    public function edit(Bill $bill)
    {
        $bill->load('items.product', 'items.unit', 'invoice.customer');

        $approvedPos = Invoice::with('customer:id,name')
            ->where('status', Invoice::STATUS_APPROVED)
            ->whereIn('user_id', auth()->user()->teamMemberIds())
            ->get(['id', 'po_number', 'customer_id', 'amount']);

        $billNumber = $bill->bill_number; // ← defined, no more undefined error
        $units      = Unit::orderBy('name')->pluck('name', 'id');
        $products   = Product::orderBy('name')->pluck('name', 'id');

        return view('bills.edit', compact('bill', 'billNumber', 'units', 'products', 'approvedPos'));
    }
    public function update(Request $request, Bill $bill)
    {
        $request->validate([
            'bill_date'    => 'required|date',
            'products'     => 'required|array|min:1',
            'products.*'   => 'required|exists:products,id',
            'quantities.*' => 'required|numeric|min:1',
            'rates.*'      => 'required|numeric|min:0',
            'amounts.*'    => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $bill) {

            $subtotal = (float) array_sum($request->amounts);
            $cgst     = (float) ($request->cgst_amount       ?? $bill->cgst_amount);
            $sgst     = (float) ($request->sgst_amount       ?? $bill->sgst_amount);
            $igst     = (float) ($request->igst_amount       ?? $bill->igst_amount);
            $transport = (float) ($request->transport_charges  ?? $bill->transport_charges);
            $extra    = (float) ($request->extra_charges      ?? $bill->extra_charges);
            $total    = $subtotal + $cgst + $sgst + $igst + $transport + $extra;

            $bill->update([
                'bill_date'         => $request->bill_date,
                'address'           => $request->address,
                'phone_number'      => $request->phone_number,
                'gst_number'        => $request->gst_number,
                'terms'             => $request->terms,
                'remarks'           => $request->remarks,
                'amount'            => $subtotal,
                'cgst_amount'       => $cgst,
                'sgst_amount'       => $sgst,
                'igst_amount'       => $igst,
                'transport_charges' => $transport,
                'extra_charges'     => $extra,
                'total_amount'      => $total,
            ]);

            $bill->items()->delete();

            foreach ($request->products as $i => $productId) {
                $bill->items()->create([
                    'product_id'  => $productId,
                    'description' => $request->descriptions[$i] ?? '',
                    'unit_id'     => $request->units[$i],
                    'quantity'    => $request->quantities[$i],
                    'rate'        => $request->rates[$i],
                    'amount'      => $request->amounts[$i],
                ]);
            }

            // Update the corresponding CRM billing_entry
            if ($bill->invoice_id) {
                $ref = 'BILL-' . str_pad($bill->bill_number, 4, '0', STR_PAD_LEFT);
                BillingEntry::where('invoice_id', $bill->invoice_id)
                    ->where('reference_number', $ref)
                    ->where('source', BillingEntry::SOURCE_CRM)
                    ->update(['billed_amount' => $total, 'billed_at' => $request->bill_date]);

                // Manually trigger recalculation since we used ::update() not save()
                Invoice::findOrFail($bill->invoice_id)->recalculateBilling();
            }
        });

        return back()->with('success', 'Bill updated.');
    }
    public function destroy(Bill $bill)
    {
        DB::transaction(function () use ($bill) {
            $invoiceId = $bill->invoice_id;
            // Remove corresponding CRM billing_entry
            if ($invoiceId) {
                $ref = 'BILL-' . str_pad($bill->bill_number, 4, '0', STR_PAD_LEFT);
                BillingEntry::where('invoice_id', $invoiceId)
                    ->where('reference_number', $ref)
                    ->where('source', BillingEntry::SOURCE_CRM)
                    ->delete();
                // BillingEntry::booted() deleted() fires → recalculateBilling()
            }

            $bill->items()->delete();
            $bill->delete();
        });

        return redirect()->route('bills.index')->with('success', 'Bill deleted.');
    }

    public function schoolFromPo(Invoice $invoice): JsonResponse
    {
        $invoice->load('customer');
        return response()->json([
            'phone_number' => $invoice->phone_number ?? $invoice->customer->phone_number,
            'gstin'        => $invoice->customer->gstin,
            'address'      => $invoice->address ?? $invoice->customer->address,
            'school_name'  => $invoice->customer->name,
        ]);
    }
}
