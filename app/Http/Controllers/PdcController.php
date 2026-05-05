<?php

namespace App\Http\Controllers;

use App\Models\Pdc;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PdcController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List all PDCs visible to current user (scoped by team).
     * Used for a PDC management page (SM / Accounts / Admin).
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $teamIds = auth()->user()->teamMemberIds();

            $pdcs = Pdc::with([
                'invoice:id,po_number,customer_id,user_id',
                'invoice.customer:id,name,school_code',
                'invoice.user:id,username,emp_code',
            ])
                ->whereHas('invoice', fn($q) => $q->whereIn('user_id', $teamIds))
                ->select(
                    'id',
                    'invoice_id',
                    'pdc_label',
                    'cheque_number',
                    'bank_name',
                    'cheque_date',
                    'amount',
                    'status'
                );

            return DataTables::of($pdcs)
                ->editColumn('cheque_date', fn($p) => $p->cheque_date->format('d M, Y'))
                ->editColumn('amount',      fn($p) => '₹' . number_format($p->amount, 2))
                ->editColumn('status',      fn($p) => $this->statusBadge($p->status))
                ->addColumn('po_number',    fn($p) => $p->invoice->po_number)
                ->editColumn('user_name_emp_code', fn($p) => $p->invoice->user->username . " ({$p->invoice->user->emp_code})")
                ->addColumn('school',       fn($p) => $p->invoice->customer->name)
                ->addColumn('sp',           fn($p) => $p->invoice->user->username)
                ->addColumn('action',       fn($p) => view('pdcs.buttons', ['pdc' => $p]))
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('pdcs.index');
    }

    /**
     * Update PDC status (pending → cleared / bounced).
     * Called by Accounts team or Admin.
     */
    public function update(Request $request, Pdc $pdc)
    {
        // Only Accounts and Admin can update PDC status
        if (!auth()->user()->isAccounts() && !auth()->user()->isAdmin()) {
            abort(403, 'Only Accounts team can update PDC status.');
        }

        $request->validate([
            'status' => 'required|in:pending,cleared,bounced',
        ]);

        $pdc->update(['status' => $request->status]);

        // If bounced, we may want to flag the invoice for follow-up
        // (business logic — left for future enhancement)

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "PDC marked as {$request->status}.",
                'status'  => $request->status,
            ]);
        }

        return back()->with('success', "Cheque {$pdc->cheque_number} marked as {$request->status}.");
    }

    private function statusBadge(string $status): string
    {
        $map = [
            'pending' => 'warning',
            'cleared' => 'success',
            'bounced' => 'danger',
        ];
        $color = $map[$status] ?? 'secondary';
        return "<span class=\"badge bg-{$color}\">" . ucfirst($status) . "</span>";
    }
}
