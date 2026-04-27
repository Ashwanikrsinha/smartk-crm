<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\BillingEntry;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // -------------------------------------------------------
    // INDEX — Accounts dashboard with both B and D editable
    // -------------------------------------------------------

    public function index(Request $request)
    {
        if (!auth()->user()->isAccounts() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $spId     = $request->input('sp_id');
        $schoolId = $request->input('school_id');
        $poNumber = $request->input('po_number');
        $month    = $request->input('month');
        $year     = $request->input('year', date('Y'));

        $rows = Invoice::with(['user:id,username', 'customer:id,name,school_code'])
            ->where('status', Invoice::STATUS_APPROVED)
            ->when($spId,     fn($q) => $q->where('user_id', $spId))
            ->when($schoolId, fn($q) => $q->where('customer_id', $schoolId))
            ->when($poNumber, fn($q) => $q->where('po_number', 'like', "%{$poNumber}%"))
            ->when($month, function ($q) use ($month) {
                $q->whereYear('invoice_date',  substr($month, 0, 4))
                    ->whereMonth('invoice_date', substr($month, 5, 2));
            }, fn($q) => $q->whereYear('invoice_date', $year))
            ->select(
                'id',
                'po_number',
                'invoice_date',
                'user_id',
                'customer_id',
                'amount',
                'billing_amount',
                'collected_amount',
                'outstanding_amount',
                'pending_po_amount'
            )
            ->orderByDesc('invoice_date')
            ->get();

        $totals = [
            'po_amount'        => $rows->sum('amount'),
            'billing_amount'   => $rows->sum('billing_amount'),
            'pending_po'       => $rows->sum('pending_po_amount'),
            'collected'        => $rows->sum('collected_amount'),
            'outstanding'      => $rows->sum('outstanding_amount'),
        ];

        $allSps  = User::salesPersons()->active()->orderBy('username')->get(['id', 'username']);
        $schools = Customer::orderBy('name')->get(['id', 'name', 'school_code']);

        return view('collections.index', compact(
            'rows',
            'totals',
            'allSps',
            'schools',
            'spId',
            'schoolId',
            'poNumber',
            'month',
            'year'
        ));
    }

    // -------------------------------------------------------
    // BULK STORE — submit both billing (B) and collection (D)
    // -------------------------------------------------------

    public function bulkStore(Request $request)
    {
        if (!auth()->user()->isAccounts() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'collections'                       => 'required|array',
            'collections.*.invoice_id'          => 'required|exists:invoices,id',
            'collections.*.billed_amount'       => 'nullable|numeric|min:0',
            'collections.*.collected_amount'    => 'nullable|numeric|min:0',
            'payment_mode'                      => 'required|in:cheque,neft,upi,cash',
            'collected_at'                      => 'required|date',
            'billing_source'                    => 'nullable|in:manual,crm',
            'billing_reference'                 => 'nullable|string|max:100',
            'reference_number'                  => 'nullable|string|max:100',
            'collection_remarks'                => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request) {

            foreach ($request->collections as $data) {
                $invoice = Invoice::findOrFail($data['invoice_id']);

                // ── Update B (Billing Amount) if entered ────────
                $billedAmt = (float) ($data['billed_amount'] ?? 0);
                if ($billedAmt > 0) {
                    BillingEntry::create([
                        'invoice_id'       => $invoice->id,
                        'billed_amount'    => $billedAmt,
                        'source'           => $request->billing_source ?? BillingEntry::SOURCE_MANUAL,
                        'reference_number' => $request->billing_reference,
                        'remarks'          => 'Manual billing entry by Accounts team',
                        'entered_by'       => auth()->id(),
                        'billed_at'        => $request->collected_at,
                    ]);
                    // BillingEntry::booted() → recalculateBilling() auto fires
                }

                // ── Update D (Collection Amount) if entered ─────
                $collectedAmt = (float) ($data['collected_amount'] ?? 0);
                if ($collectedAmt > 0) {
                    Collection::create([
                        'invoice_id'       => $invoice->id,
                        'collected_amount' => $collectedAmt,
                        'payment_mode'     => $request->payment_mode,
                        'reference_number' => $request->reference_number,
                        'remarks'          => $request->collection_remarks,
                        'collected_by'     => auth()->id(),
                        'collected_at'     => $request->collected_at,
                    ]);
                    // Collection::booted() → recalculateCollections() auto fires
                }
            }
        });

        return redirect()->route('collections.index')
            ->with('success', 'Billing and collection records updated. All dashboards refreshed.');
    }

    // -------------------------------------------------------
    // AJAX — collection history for a specific PO
    // -------------------------------------------------------

    public function forInvoice(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        return response()->json([
            'billing_entries' => $invoice->billingEntries()
                ->with('enteredBy:id,username')
                ->orderByDesc('billed_at')
                ->get(),
            'collections' => $invoice->collections()
                ->with('collectedBy:id,username')
                ->orderByDesc('collected_at')
                ->get(),
            'cleared_pdcs' => $invoice->pdcs()
                ->where('status', 'cleared')
                ->get(),
        ]);
    }

    // -------------------------------------------------------
    // EDIT / UPDATE a single collection entry
    // -------------------------------------------------------

    public function edit(Collection $collection)
    {
        if (!auth()->user()->isAccounts() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $collection->load('invoice.customer', 'collectedBy');

        return view('collections.edit', compact('collection'));
    }

    public function update(Request $request, Collection $collection)
    {
        if (!auth()->user()->isAccounts() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'collected_amount' => 'required|numeric|min:0.01',
            'payment_mode'     => 'required|in:cheque,neft,upi,cash',
            'collected_at'     => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'remarks'          => 'nullable|string|max:500',
        ]);

        $collection->update($request->only([
            'collected_amount',
            'payment_mode',
            'collected_at',
            'reference_number',
            'remarks',
        ]));
        // Collection::booted() fires → recalculateCollections()

        return back()->with('success', 'Collection entry updated.');
    }
}
