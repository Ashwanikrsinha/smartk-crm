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
        $spId         = $request->input('sp_id');
        $schoolId     = $request->input('school_id');
        $leadSrcId    = $request->input('lead_source_id');
        $state        = $request->input('state');
        $status       = $request->input('status');
        $month        = $request->input('month');
        $dateFrom     = $request->input('date_from');
        $dateTo       = $request->input('date_to');
        $recordType   = $request->input('record_type', 'all');
        if (!in_array($recordType, ['all', 'po', 'opening_balance'])) {
            $recordType = 'all';
        }

        $sessionInput = $request->input('session', $request->input('year', 'all'));
        $sessions     = $this->availableSessions();
        $sessionRange = $this->sessionDateRange($sessionInput);
        $session      = $sessionRange['session'];
        $year         = $sessionRange['start_year'];

        // ── Resolve SP / Manager hierarchy ────────────────
        $selectedMember  = $spId ? User::find($spId) : null;
        $filteredUserIds = $selectedMember ? $selectedMember->subordinateAndSelfIds() : $teamIds;
        if (!auth()->user()->isAdmin() && !auth()->user()->isBusinessManager() && !auth()->user()->isAccounts()) {
            $filteredUserIds = array_values(array_intersect($filteredUserIds, $teamIds));
        }

        // ── Build query ────────────────────────────────────
        $query = Invoice::with([
            'user:id,username,reportive_id,emp_code',
            'user.reportiveTo:id,username,emp_code',
            'customer:id,name,school_code,state,city,lead_source_id',
            'customer.leadSource:id,name',
        ])
            ->whereIn('user_id', $filteredUserIds)
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

        if ($schoolId)  $query->where('customer_id', $schoolId);
        if ($status)    $query->where('status', $status);

        if ($leadSrcId) {
            $query->whereHas('customer', fn($q) => $q->where('lead_source_id', $leadSrcId));
        }
        if ($state) {
            $query->whereHas('customer', fn($q) => $q->where('state', $state));
        }

        $filterSessionYear = null;
        if ($month) {
            $query->whereYear('invoice_date',  substr($month, 0, 4))
                ->whereMonth('invoice_date', substr($month, 5, 2));
            $mYear  = (int) substr($month, 0, 4);
            $mMonth = (int) substr($month, 5, 2);
            $filterSessionYear = $mMonth >= 7 ? $mYear : $mYear - 1;
        } elseif ($dateFrom && $dateTo) {
            $query->whereBetween('invoice_date', [$dateFrom, $dateTo]);
            $fromYear = (int) substr($dateFrom, 0, 4);
            $fromMonth = (int) substr($dateFrom, 5, 2);
            $filterSessionYear = $fromMonth >= 7 ? $fromYear : $fromYear - 1;
        } elseif ($sessionInput && str_starts_with($sessionInput, 'year_')) {
            $calYear = (int) substr($sessionInput, 5);
            $query->whereYear('invoice_date', $calYear);
            $filterSessionYear = $calYear;
        } elseif ($sessionInput && $sessionInput !== 'all') {
            $query->whereBetween('invoice_date', [$sessionRange['start_date'], $sessionRange['end_date']]);
            $filterSessionYear = $year;
        }

        // ── Fetch opening balances for this session / year filter ───
        $obQuery = \App\Models\SchoolOpeningBalance::with([
            'salesPerson:id,username,emp_code',
            'salesPerson.reportiveTo:id,username,emp_code',
            'customer:id,name,school_code,state,city,lead_source_id',
            'customer.leadSource:id,name',
        ]);

        if ($filterSessionYear !== null) {
            $obQuery->forSession($filterSessionYear);
        }

        if ($spId) {
            $obQuery->whereIn('user_id', $filteredUserIds);
        } else {
            if (!auth()->user()->isAdmin() && !auth()->user()->isBusinessManager() && !auth()->user()->isAccounts()) {
                $obQuery->where(function($q) use ($teamIds) {
                    $q->whereIn('user_id', $teamIds)->orWhereNull('user_id');
                });
            }
        }
        if ($schoolId)  $obQuery->where('customer_id', $schoolId);
        if ($leadSrcId) $obQuery->whereHas('customer', fn($q) => $q->where('lead_source_id', $leadSrcId));
        if ($state)     $obQuery->whereHas('customer', fn($q) => $q->where('state', $state));

        // ── Summary totals over ALL matching records ──────────────
        if ($recordType !== 'opening_balance') {
            $aggregates = (clone $query)->select(DB::raw('
                SUM(amount) as po_amount,
                SUM(billing_amount) as billing_amount,
                SUM(gst_amount) as gst_amount,
                SUM(pending_po_amount) as pending_po_amount,
                SUM(collected_amount) as collected_amount,
                SUM(outstanding_amount) as outstanding_amount
            '))->first();

            $poAmt      = (float) ($aggregates->po_amount ?? 0);
            $billingAmt = (float) ($aggregates->billing_amount ?? 0);
            $gstAmt     = (float) ($aggregates->gst_amount ?? 0);
            $pendingAmt = (float) ($aggregates->pending_po_amount ?? 0);
            $collAmt    = (float) ($aggregates->collected_amount ?? 0);
            $outstAmt   = (float) ($aggregates->outstanding_amount ?? 0);
        } else {
            $poAmt = 0; $billingAmt = 0; $gstAmt = 0; $pendingAmt = 0; $collAmt = 0; $outstAmt = 0;
        }

        if ($recordType !== 'po') {
            $obAggregates = (clone $obQuery)->select(DB::raw('
                SUM(opening_collected) as ob_collected,
                SUM(opening_outstanding) as ob_outstanding
            '))->first();

            $obCollAmt  = (float) ($obAggregates->ob_collected ?? 0);
            $obOutstAmt = (float) ($obAggregates->ob_outstanding ?? 0);
        } else {
            $obCollAmt  = 0;
            $obOutstAmt = 0;
        }

        $netBilled  = max(0, $billingAmt - $gstAmt);

        $totals = [
            'po_amount'      => $poAmt,
            'billing_amount' => $billingAmt,
            'gst_amount'     => $gstAmt,
            'net_billed'     => $netBilled,
            'pending_po'     => $pendingAmt,
            'collected'      => (float) ($collAmt + $obCollAmt),
            'outstanding'    => (float) ($outstAmt + $obOutstAmt),
        ];

        // ── Unified Pagination: exactly perPage records per page ────────────────
        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [10, 20, 50, 100, 200])) {
            $perPage = 20;
        }
        $page = (int) $request->input('page', 1);
        if ($page < 1) $page = 1;

        $poCount = ($recordType !== 'opening_balance') ? (clone $query)->count() : 0;
        $obCount = ($recordType !== 'po') ? (clone $obQuery)->count() : 0;
        $totalCount = $poCount + $obCount;

        $offset = ($page - 1) * $perPage;

        $pageInvoices = collect();
        $pageOBs      = collect();

        if ($recordType === 'po') {
            $pageInvoices = (clone $query)->orderByDesc('invoice_date')
                ->skip($offset)->take($perPage)->get();
        } elseif ($recordType === 'opening_balance') {
            $pageOBs = (clone $obQuery)->orderByDesc('id')
                ->skip($offset)->take($perPage)->get();
        } else {
            // 'all' records: Purchase orders first, then opening balances
            if ($offset < $poCount) {
                $poTake = min($perPage, $poCount - $offset);
                $pageInvoices = (clone $query)->orderByDesc('invoice_date')
                    ->skip($offset)->take($poTake)->get();

                $obTake = $perPage - $poTake;
                if ($obTake > 0) {
                    $pageOBs = (clone $obQuery)->orderByDesc('id')
                        ->skip(0)->take($obTake)->get();
                }
            } else {
                $obOffset = $offset - $poCount;
                $pageOBs = (clone $obQuery)->orderByDesc('id')
                    ->skip($obOffset)->take($perPage)->get();
            }
        }

        $currentPageItems = $pageInvoices->concat($pageOBs);

        $rows = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $totalCount,
            $perPage,
            $page,
            [
                'path'  => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

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
            'year',
            'session',
            'sessions',
            'perPage',
            'recordType',
            'poCount',
            'obCount'
        ));
    }

    private function sessionDateRange(?string $sessionInput): array
    {
        if ($sessionInput === 'all' || empty($sessionInput)) {
            return [
                'session'    => 'all',
                'start_year' => null,
                'start_date' => null,
                'end_date'   => null,
            ];
        }

        if (str_starts_with($sessionInput, 'year_')) {
            $y = (int) substr($sessionInput, 5);
            return [
                'session'    => $sessionInput,
                'start_year' => $y,
                'start_date' => "{$y}-01-01",
                'end_date'   => "{$y}-12-31",
            ];
        }

        if (strpos($sessionInput, '-') !== false) {
            $parts     = explode('-', $sessionInput);
            $startYear = (int) $parts[0];
        } elseif (is_numeric($sessionInput)) {
            $startYear = (int) $sessionInput;
        } else {
            $month     = (int) date('n');
            $year      = (int) date('Y');
            $startYear = ($month >= 7) ? $year : $year - 1;
        }

        $session = $startYear . '-' . ($startYear + 1);

        return [
            'session'    => $session,
            'start_year' => $startYear,
            'start_date' => "{$startYear}-07-01",
            'end_date'   => ($startYear + 1) . "-06-30",
        ];
    }

    private function availableSessions(): array
    {
        $currentStart = ((int) date('n') >= 7) ? (int) date('Y') : (int) date('Y') - 1;
        $sessions     = ['all'];

        for ($i = 0; $i <= 3; $i++) {
            $start = $currentStart - $i;
            $sessions[] = $start . '-' . ($start + 1);
        }

        $sessions[] = 'year_' . date('Y');
        $sessions[] = 'year_' . (date('Y') - 1);

        return $sessions;
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
