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
        $spId        = $request->input('sp_id');
        $schoolId    = $request->input('school_id');
        $leadSrcId   = $request->input('lead_source_id');
        $state       = $request->input('state');
        $status      = $request->input('status');
        $month       = $request->input('month');
        $dateFrom    = $request->input('date_from');
        $dateTo      = $request->input('date_to');
        $year        = $request->input('year', date('Y'));

        // ── Build query ────────────────────────────────────
        $query = Invoice::with([
            'user:id,username,reportive_id',
            'user.reportiveTo:id,username',
            'customer:id,name,school_code,state,city,lead_source_id',
            'customer.leadSource:id,name',
        ])
            ->whereIn('user_id', $teamIds)
            ->select(
                'id',
                'po_number',
                'invoice_date',
                'user_id',
                'customer_id',
                'status',
                'amount',
                'billing_amount',
                'collected_amount',
                'outstanding_amount',
                'pending_po_amount',
                'delivery_due_date'
            );

        // Apply all filters
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

        $rows = $query->orderByDesc('invoice_date')->get();

        // ── Summary totals ─────────────────────────────────
        $totals = [
            'po_amount'        => $rows->sum('amount'),
            'billing_amount'   => $rows->sum('billing_amount'),
            'pending_po'       => $rows->sum('pending_po_amount'),
            'collected'        => $rows->sum('collected_amount'),
            'outstanding'      => $rows->sum('outstanding_amount'),
        ];

        // ── Filter dropdowns ───────────────────────────────
        $teamMembers = User::whereIn('id', $teamIds)
            ->salesPersons()
            ->active()
            ->orderBy('username')
            ->get(['id', 'username']);

        $schools = Customer::whereHas('invoices', fn($q) => $q->whereIn('user_id', $teamIds))
            ->orderBy('name')
            ->get(['id', 'name', 'school_code']);

        $leadSources = LeadSource::orderBy('name')->get(['id', 'name']);

        $states = Customer::distinct()->whereNotNull('state')
            ->orderBy('state')->pluck('state');

        // Store filters in session for export
        session(['report_filters' => $request->all()]);
        // return $rows;

        return view('reports.index', compact(
            'rows',
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
    // Export to Excel
    // -------------------------------------------------------

    public function export(Request $request)
    {
        // $this->authorize('export', Invoice::class);
        if(!auth()->user()->hasPermission('export_reports')) {
            return back()->with('error', 'You do not have permission to export reports.');
        }

        $filename = 'SmartK_Report_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(
            new PurchaseOrdersExport($request->all(), auth()->user()),
            $filename
        );
    }
}
