<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\DispatchItem;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DispatchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // -------------------------------------------------------
    // INDEX — Warehouse dashboard / dispatch list
    // -------------------------------------------------------

    public function index(Request $request)
    {
        $user    = auth()->user();
        $teamIds = $user->teamMemberIds();

        if ($request->ajax()) {
            $dispatches = Dispatch::with([
                'invoice:id,po_number,customer_id,invoice_date,delivery_due_date,amount,user_id',
                'invoice.customer:id,name,school_code',
                'invoice.user:id,username',
                'invoice.invoiceItems.product.category',
                'dispatchedBy:id,username',
                'items',
            ])
            ->whereHas('invoice', function($q) use ($teamIds, $user) {
                if (!$user->role?->name === 'Warehouse') {
                    $q->whereIn('user_id', $teamIds);
                }
            })
            ->select('id', 'invoice_id', 'dispatch_number', 'dispatch_date',
                     'bilty_number', 'challan_number', 'vehicle_number', 'dispatched_by');

            return DataTables::of($dispatches)
                ->editColumn('dispatch_date', fn($d) => $d->dispatch_date->format('d M, Y'))
                ->addColumn('po_number',  fn($d) => $d->invoice->po_number)
                ->addColumn('school',     fn($d) => $d->invoice->customer->name)
                ->addColumn('items_summary', function ($d) {
                    return $d->items->map(fn($i) =>
                        $i->product->category?->name . ' / ' . $i->product->name .
                        ' — ' . $i->quantity_dispatched . ' units'
                    )->implode('<br>');
                })
                ->addColumn('action', fn($d) => view('dispatches.buttons', ['dispatch' => $d])->render())
                ->rawColumns(['items_summary', 'action'])
                ->make(true);
        }

        // For warehouse dashboard — approved POs with pending dispatch
        $query = Invoice::with([
            'customer:id,name,school_code',
            'invoiceItems.product.category',
            'dispatches.items',
        ])
        ->where('status', Invoice::STATUS_APPROVED);

        // Warehouse sees everything, others see team data
        if ($user->role?->name !== 'Warehouse') {
            $query->whereIn('user_id', $teamIds);
        }

        $approvedPos = $query->orderByDesc('invoice_date')->get();

        // Calculate remaining qty for each PO to filter out fully dispatched ones
        foreach ($approvedPos as $po) {
            $dispatched = \App\Models\DispatchItem::whereHas('dispatch', fn($q) => $q->where('invoice_id', $po->id))
                ->selectRaw('invoice_item_id, SUM(quantity_dispatched) as done')
                ->groupBy('invoice_item_id')
                ->get()->keyBy('invoice_item_id');

            foreach ($po->invoiceItems as $item) {
                $item->remaining_qty = max($item->quantity - ($dispatched[$item->id]->done ?? 0), 0);
            }

            $po->is_fully_dispatched = $po->invoiceItems->every(fn($i) => $i->remaining_qty <= 0);
        }

        // Filter out fully dispatched POs
        $approvedPos = $approvedPos->reject(fn($p) => $p->is_fully_dispatched);

        return view('dispatches.index', compact('approvedPos'));
    }

    // -------------------------------------------------------
    // CREATE DISPATCH — for a specific PO
    // -------------------------------------------------------

    public function create(Request $request)
    {
        $invoice = Invoice::with([
            'customer:id,name,school_code,address,city,state',
            'invoiceItems.product.category',
            'dispatches.items',
        ])
        ->where('status', Invoice::STATUS_APPROVED)
        ->findOrFail($request->invoice_id);

        // Build per-item remaining quantities
        $dispatched = DispatchItem::whereHas('dispatch', fn($q) => $q->where('invoice_id', $invoice->id))
            ->selectRaw('invoice_item_id, SUM(quantity_dispatched) as total_dispatched')
            ->groupBy('invoice_item_id')
            ->get()
            ->keyBy('invoice_item_id');

        $items = $invoice->invoiceItems->map(function ($item) use ($dispatched) {
            $totalDispatched = $dispatched[$item->id]->total_dispatched ?? 0;
            $remaining       = max($item->quantity - $totalDispatched, 0);
            return [
                'id'                  => $item->id,
                'product_id'          => $item->product_id,
                'product_name'        => $item->product->name,
                'category_name'       => $item->product->category?->name ?? '—',
                'ordered_qty'         => $item->quantity,
                'dispatched_qty'      => $totalDispatched,
                'remaining_qty'       => $remaining,
            ];
        });

        return view('dispatches.create', compact('invoice', 'items'));
    }

    // -------------------------------------------------------
    // STORE
    // -------------------------------------------------------

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'        => 'required|exists:invoices,id',
            'dispatch_date'     => 'required|date',
            'bilty_number'      => 'nullable|string|max:100',
            'challan_number'    => 'nullable|string|max:100',
            'vehicle_number'    => 'nullable|string|max:50',
            'driver_name'       => 'nullable|string|max:100',
            'driver_phone'      => 'nullable|string|max:15',
            'remarks'           => 'nullable|string|max:500',
            'items'             => 'required|array|min:1',
            'items.*.invoice_item_id'       => 'required|exists:invoice_items,id',
            'items.*.product_id'            => 'required|exists:items,id',
            'items.*.quantity_dispatched'   => 'nullable|numeric|min:0',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        // Check if at least one item has quantity > 0
        $hasQty = false;
        foreach ($request->items as $item) {
            if ((float)($item['quantity_dispatched'] ?? 0) > 0) {
                $hasQty = true;
                break;
            }
        }

        if (!$hasQty) {
            return back()->withInput()->with('error', 'Please enter quantity for at least one item to dispatch.');
        }

        DB::transaction(function () use ($request, $invoice) {
            $dispatch = Dispatch::create([
                'invoice_id'      => $invoice->id,
                'dispatch_number' => Dispatch::generateNumber(),
                'dispatch_date'   => $request->dispatch_date,
                'bilty_number'    => $request->bilty_number,
                'challan_number'  => $request->challan_number,
                'vehicle_number'  => $request->vehicle_number,
                'driver_name'     => $request->driver_name,
                'driver_phone'    => $request->driver_phone,
                'remarks'         => $request->remarks,
                'dispatched_by'   => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $qty = (float)($item['quantity_dispatched'] ?? 0);
                if ($qty <= 0) continue;

                $dispatch->items()->create([
                    'invoice_item_id'     => $item['invoice_item_id'],
                    'product_id'          => $item['product_id'],
                    'quantity_dispatched' => $qty,
                    'remarks'             => $item['remarks'] ?? null,
                ]);
            }
        });

        return redirect()->route('dispatches.index')
            ->with('success', 'Dispatch recorded successfully.');
    }

    // -------------------------------------------------------
    // SHOW — dispatch detail
    // -------------------------------------------------------

    public function show(Dispatch $dispatch)
    {
        $dispatch->load([
            'invoice.customer',
            'invoice.invoiceItems.product.category',
            'items.product.category',
            'dispatchedBy',
        ]);

        return view('dispatches.show', compact('dispatch'));
    }

    // -------------------------------------------------------
    // PO DISPATCH SUMMARY — how much left to dispatch
    // -------------------------------------------------------

    public function poSummary(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $dispatched = DispatchItem::whereHas('dispatch',
            fn($q) => $q->where('invoice_id', $invoice->id))
            ->selectRaw('invoice_item_id, SUM(quantity_dispatched) as total_dispatched')
            ->groupBy('invoice_item_id')
            ->get()
            ->keyBy('invoice_item_id');

        $invoice->load('invoiceItems.product.category', 'dispatches');

        $summary = $invoice->invoiceItems->map(function ($item) use ($dispatched) {
            $done = $dispatched[$item->id]->total_dispatched ?? 0;
            return [
                'product'          => $item->product->name,
                'category'         => $item->product->category?->name ?? '—',
                'ordered_qty'      => $item->quantity,
                'dispatched_qty'   => $done,
                'remaining_qty'    => max($item->quantity - $done, 0),
                'fully_dispatched' => $done >= $item->quantity,
            ];
        });

        return response()->json([
            'po_number'        => $invoice->po_number,
            'dispatch_count'   => $invoice->dispatches->count(),
            'items'            => $summary,
        ]);
    }
}
