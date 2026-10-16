<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\User;
use App\Models\Visit;
use App\Models\LeadSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseOrdersExport;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // -------------------------------------------------------
    // Main reports page (GET /reports)
    // -------------------------------------------------------

    public function index(Request $request)
    {
        $user    = auth()->user();
        $teamIds = $user->teamMemberIds();

        // ── Filters ────────────────────────────────────────
        $spId      = $request->input('sp_id');
        $schoolId  = $request->input('school_id');
        $leadSrcId = $request->input('lead_source_id');
        $state     = $request->input('state');
        $status    = $request->input('status');
        $month     = $request->input('month');
        $dateFrom  = $request->input('date_from');
        $dateTo    = $request->input('date_to');
        $year      = $request->input('year', date('Y'));

        // ── Build query ────────────────────────────────────
        $query = Invoice::with([
            'user:id,username,reportive_id,emp_code',
            'user.reportiveTo:id,username,emp_code',
            'customer:id,name,school_code,state,city,lead_source_id',
            'customer.leadSource:id,name',
        ])
            ->whereIn('user_id', $teamIds)
            ->select(
                'id',
                'po_number',
                'invoice_date',
                'co_branding',
                'order_type',
                'user_id',
                'customer_id',
                'status',
                'amount',
                'billing_amount',
                'gst_amount',
                'collected_amount',
                'outstanding_amount',
                'pending_po_amount',
                'delivery_due_date'
            );

        if ($spId)      $query->where('user_id', $spId);
        if ($schoolId)  $query->where('customer_id', $schoolId);
        if ($status)    $query->where('status', $status);

        if ($leadSrcId) {
            $query->whereHas('customer', fn($q) => $q->where('lead_source_id', $leadSrcId));
        }
        if ($state) {
            $query->whereHas('customer', fn($q) => $q->where('state', $state));
        }
        if ($month) {
            $query->whereYear('invoice_date',  substr($month, 0, 4))
                ->whereMonth('invoice_date', substr($month, 5, 2));
        } elseif ($dateFrom && $dateTo) {
            $query->whereBetween('invoice_date', [$dateFrom, $dateTo]);
        } else {
            $query->whereYear('invoice_date', $year);
        }

        // ── Fetch opening balances for this session / year filter ───
        $filterSessionYear = null;
        if ($month) {
            $mYear  = (int) substr($month, 0, 4);
            $mMonth = (int) substr($month, 5, 2);
            $filterSessionYear = $mMonth >= 7 ? $mYear : $mYear - 1;
        } elseif ($dateFrom && $dateTo) {
            $fromYear = (int) substr($dateFrom, 0, 4);
            $fromMonth = (int) substr($dateFrom, 5, 2);
            $filterSessionYear = $fromMonth >= 7 ? $fromYear : $fromYear - 1;
        } else {
            $filterSessionYear = (int) ($request->input('session_year', $year));
        }

        $obQuery = \App\Models\SchoolOpeningBalance::with([
            'salesPerson:id,username,emp_code',
            'salesPerson.reportiveTo:id,username,emp_code',
            'customer:id,name,school_code,state,city,lead_source_id',
            'customer.leadSource:id,name',
        ])
        ->forSession($filterSessionYear);

        if (!auth()->user()->isAdmin() && !auth()->user()->isBusinessManager() && !auth()->user()->isAccounts()) {
            $obQuery->where(function($q) use ($teamIds) {
                $q->whereIn('user_id', $teamIds)->orWhereNull('user_id');
            });
        }
        if ($spId)      $obQuery->where('user_id', $spId);
        if ($schoolId)  $obQuery->where('customer_id', $schoolId);
        if ($leadSrcId) $obQuery->whereHas('customer', fn($q) => $q->where('lead_source_id', $leadSrcId));
        if ($state)     $obQuery->whereHas('customer', fn($q) => $q->where('state', $state));

        $openingBalanceRows = $obQuery->get();

        // ── Summary totals over ALL matching records ──────────────
        $aggregates = (clone $query)->selectRaw('
            SUM(amount) as po_amount,
            SUM(billing_amount) as billing_amount,
            SUM(gst_amount) as gst_amount,
            SUM(pending_po_amount) as pending_po_amount,
            SUM(collected_amount) as collected_amount,
            SUM(outstanding_amount) as outstanding_amount
        ')->first();

        $poAmt      = (float) ($aggregates->po_amount ?? 0);
        $billingAmt = (float) ($aggregates->billing_amount ?? 0);
        $gstAmt     = (float) ($aggregates->gst_amount ?? 0);
        $pendingAmt = (float) ($aggregates->pending_po_amount ?? 0);
        $collAmt    = (float) ($aggregates->collected_amount ?? 0);
        $outstAmt   = (float) ($aggregates->outstanding_amount ?? 0);
        $netBilled  = max(0, $billingAmt - $gstAmt);

        $totals = [
            'po_amount'      => $poAmt,
            'billing_amount' => $billingAmt,
            'gst_amount'     => $gstAmt,
            'net_billed'     => $netBilled,
            'pending_po'     => $pendingAmt,
            'collected'      => (float) ($collAmt + $openingBalanceRows->sum('opening_collected')),
            'outstanding'    => (float) ($outstAmt + $openingBalanceRows->sum('opening_outstanding')),
        ];

        // ── Paginate 20 records at a time ────────────────────────
        $rows = $query->orderByDesc('invoice_date')->paginate(20)->withQueryString();

        // ── Filter dropdowns ───────────────────────────────
        $teamMembers = User::whereIn('id', $teamIds)
            ->SalesPersonOrManager()
            ->active()
            ->orderBy('username')
            ->get(['id', 'username', 'emp_code']);

        $schools = Customer::whereHas('invoices', fn($q) => $q->whereIn('user_id', $teamIds))
            ->orWhereHas('openingBalances')
            ->orderBy('name')
            ->get(['id', 'name', 'school_code']);

        $leadSources = LeadSource::orderBy('name')->get(['id', 'name']);

        $states = Customer::distinct()->whereNotNull('state')
            ->orderBy('state')->pluck('state');

        session(['report_filters' => $request->all()]);

        return view('reports.index', compact(
            'rows',
            'openingBalanceRows',
            'totals',
            'teamMembers',
            'schools',
            'leadSources',
            'states',
            'spId',
            'schoolId',
            'leadSrcId',
            'state',
            'status',
            'month',
            'dateFrom',
            'dateTo',
            'year'
        ));
    }

    // -------------------------------------------------------
    // Export to Excel — Marketing blocked entirely
    // -------------------------------------------------------

    public function export(Request $request)
    {
        if (!auth()->user()->hasPermission('export_reports')) {
            return back()->with('error', 'You do not have permission to export reports.');
        }

        $filename = 'SmartK_Report_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(
            new PurchaseOrdersExport($request->all(), auth()->user()),
            $filename
        );
    }

    public function exportPoLog(Request $request, $invoiceId = null)
    {
        if (auth()->user()->isMarketing()) {
            abort(403, 'Marketing team cannot export report data.');
        }
        if (!auth()->user()->hasPermission('export_reports')) {
            return back()->with('error', 'You do not have permission to export reports.');
        }

        $filename = 'SmartK_PO_Logs_' . ($invoiceId ? "Invoice_{$invoiceId}_" : "All_") . date('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new \App\Exports\PoLogExport($invoiceId, $request->all()),
            $filename
        );
    }
}
