<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\User;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = auth()->user();
        if ($user->isAdmin() || $user->isBusinessManager()) {
            return $this->adminDashboard($request, $user);
        }

        if ($user->isSalesManager() || $user->isAdmin()) {
            return $this->managerDashboard($request, $user);
        }

        if ($user->isSalesPerson()) {
            return $this->salesPersonDashboard($request, $user);
        }

        if ($user->isAccounts()) {
            return $this->accountsDashboard($request, $user);
        }

        if ($user->role?->name === 'Warehouse') {
            return $this->warehouseDashboard($request, $user);
        }

        // Fallback for Warehouse / other roles
        return view('dashboard.default');
    }

    private function warehouseDashboard(Request $request, User $user)
    {
        $year      = $request->input('year', date('Y'));
        $month     = $request->input('month');
        $dateFrom  = $request->input('date_from');
        $dateTo    = $request->input('date_to');
        $duedateFrom  = $request->input('due_date_from');
        $duedateTo    = $request->input('due_date_to');
        $productType = $request->input('product_type', 'all');
        $productId   = $request->input('product_id', 'all');

        $totalOrderedQty = 0;
        $totalDoneQty    = 0;

        // Approved POs only for warehouse.
        $query = Invoice::with([
            'customer:id,name,school_code',
            'invoiceItems.product.category',
            'invoiceItems.unit',
            'dispatches.items'
        ])
            ->where('status', Invoice::STATUS_APPROVED);

        // Apply filters
        if ($month) {
            $query->whereYear('invoice_date', substr($month, 0, 4))
                ->whereMonth('invoice_date', substr($month, 5, 2));
        } elseif ($dateFrom && $dateTo) {
            $query->whereBetween('invoice_date', [$dateFrom, $dateTo]);
        } else {
            $query->whereYear('invoice_date', $year);
        }

        if ($duedateFrom && $duedateTo) {
            $query->whereBetween('delivery_due_date', [$duedateFrom, $duedateTo]);
        }



        if ($productType !== 'all') {
            $query->whereHas('invoiceItems.product', function ($q) use ($productType) {
                $q->where('category_id', $productType);
            });
        }

        if ($productId !== 'all') {
            $query->whereHas('invoiceItems', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
        }

        // if ($user->isBusinessManager()) {
        //     $query->where('invoiceItems.product_id', '!=', 0);
        // }

        $allRows = $query->orderBy('delivery_due_date', 'asc')->get();

        // Calculate remaining qty for each item (WE NEED THIS FOR ALL TO CALCULATE TOTALS)
        // foreach ($allRows as $invoice) {
        //     $dispatched = \App\Models\DispatchItem::whereHas('dispatch', fn($q) => $q->where('invoice_id', $invoice->id))
        //         ->selectRaw('invoice_item_id, SUM(quantity_dispatched) as done')
        //         ->groupBy('invoice_item_id')
        //         ->get()->keyBy('invoice_item_id');

        //     foreach ($invoice->invoiceItems as $item) {
        //         $item->done_qty = (float)($dispatched[$item->id]->done ?? 0);
        //         $item->remaining_qty = round((float)$item->quantity - $item->done_qty, 3);
        //         if ($item->remaining_qty < 0) $item->remaining_qty = 0;

        //         $totalOrderedQty += (float)$item->quantity;
        //         $totalDoneQty    += $item->done_qty;
        //     }

        //     $invoice->is_fully_dispatched = $invoice->invoiceItems->every(fn($i) => $i->remaining_qty <= 0);
        // }
        foreach ($allRows as $invoice) {

            $dispatched = \App\Models\DispatchItem::whereHas('dispatch', function ($q) use ($invoice) {
                $q->where('invoice_id', $invoice->id);
            })
                ->selectRaw('invoice_item_id, SUM(quantity_dispatched) as done')
                ->groupBy('invoice_item_id')
                ->get()
                ->keyBy('invoice_item_id');

            foreach ($invoice->invoiceItems as $key => $item) {

                $item->done_qty = (float)($dispatched[$item->id]->done ?? 0);
                $item->remaining_qty = round(
                    (float)$item->quantity - $item->done_qty,
                    3
                );

                if ($item->remaining_qty < 0) {
                    $item->remaining_qty = 0;
                }

                /*
        |--------------------------------------------------------------------------
        | ITEM LEVEL FILTERING
        |--------------------------------------------------------------------------
        */

                // Product type filter
                if (
                    $productType !== 'all' &&
                    optional($item->product)->category_id != $productType
                ) {
                    unset($invoice->invoiceItems[$key]);
                    continue;
                }

                // Product filter
                if (
                    $productId !== 'all' &&
                    $item->product_id != $productId
                ) {
                    unset($invoice->invoiceItems[$key]);
                    continue;
                }

                $totalOrderedQty += (float)$item->quantity;
                $totalDoneQty += $item->done_qty;
            }

            // reindex collection
            $invoice->setRelation(
                'invoiceItems',
                $invoice->invoiceItems->values()
            );

            $invoice->is_fully_dispatched =
                $invoice->invoiceItems->isEmpty() ||
                $invoice->invoiceItems->every(fn($i) => $i->remaining_qty <= 0);
        }

        $totalPendingQty = max($totalOrderedQty - $totalDoneQty, 0);

        // Filter rows for table (only those with something left to dispatch)
        // Since we need pagination, we should ideally do this filtering in the query,
        // but because remaining_qty is calculated in PHP, we'll paginate the collection.
        $filteredRows = $allRows->reject(fn($i) => $i->is_fully_dispatched);

        $perPage = 15;
        $page = $request->input('page', 1);
        $rows = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredRows->forPage($page, $perPage),
            $filteredRows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        $products = Product::all();
        $productTypes = Category::all();
        $totalProductTypes = collect($allRows)
            ->flatMap(fn($invoice) => $invoice->invoiceItems)
            ->pluck('product.category_id')
            ->unique()
            ->count();

        $totalProducts = collect($allRows)
            ->flatMap(fn($invoice) => $invoice->invoiceItems)
            ->pluck('product_id')
            ->unique()
            ->count();

        $totalDispatchedProducts = collect($allRows)
            ->flatMap(fn($invoice) => $invoice->invoiceItems)
            ->where('done_qty', '>', 0)
            ->pluck('product_id')
            ->unique()
            ->count();

        $totalPendingProducts = collect($allRows)
            ->flatMap(fn($invoice) => $invoice->invoiceItems)
            ->where('remaining_qty', '>', 0)
            ->pluck('product_id')
            ->unique()
            ->count();

        return view('dashboard.warehouse', compact(
            'rows',
            'totalOrderedQty',
            'totalDoneQty',
            'totalPendingQty',
            'totalProductTypes',
            'totalProducts',
            'totalDispatchedProducts',
            'totalPendingProducts',
            'year',
            'month',
            'dateFrom',
            'dateTo',
            'productType',
            'productId',
            'products',
            'productTypes',
            'duedateFrom',
            'duedateTo'
        ));
    }

    // ═══════════════════════════════════════════════════
    // SALES MANAGER / ADMIN DASHBOARD
    // ═══════════════════════════════════════════════════

    private function managerDashboard(Request $request, User $user)
    {
        $teamIds = $user->teamMemberIds();

        // ── Filters ──────────────────────────────────────
        $spId      = $request->input('sp_id');
        $schoolId  = $request->input('school_id');
        $month     = $request->input('month');     // format: 2025-04
        $dateFrom  = $request->input('date_from');
        $dateTo    = $request->input('date_to');
        $year      = $request->input('year', date('Y'));

        // ── Base query (approved POs only for financials) ─
        $baseQuery = Invoice::whereIn('user_id', $teamIds)
            ->whereIn('status', [Invoice::STATUS_APPROVED, Invoice::STATUS_SUBMITTED]);

        // Apply filters
        if ($spId)     $baseQuery->where('user_id', $spId);
        if ($schoolId) $baseQuery->where('customer_id', $schoolId);
        if ($month) {
            $baseQuery->whereYear('invoice_date', substr($month, 0, 4))
                ->whereMonth('invoice_date', substr($month, 5, 2));
        } elseif ($dateFrom && $dateTo) {
            $baseQuery->whereBetween('invoice_date', [$dateFrom, $dateTo]);
        } else {
            $baseQuery->whereYear('invoice_date', $year);
        }

        // ── WIDGET A: Total PO Amount ─────────────────────
        $totalPoAmount = (clone $baseQuery)->sum('amount');

        // ── WIDGET B: Total Sales/Billing Amount ──────────
        $totalBillingAmount = (clone $baseQuery)->sum('billing_amount');

        // ── WIDGET C: Total Pending PO Amount (A - B) ─────
        $totalPendingPo = $totalPoAmount - $totalBillingAmount;

        // ── WIDGET D: Total Collection ────────────────────
        $totalCollection = (clone $baseQuery)->sum('collected_amount');

        // ── WIDGET E: Total Outstanding (B - D) ───────────
        $totalOutstanding = $totalBillingAmount - $totalCollection;

        // ── WIDGET: No. of Signed Schools ────────────────
        $signedSchoolsQuery = Invoice::whereIn('user_id', $teamIds)
            ->where('status', Invoice::STATUS_APPROVED);
        if ($spId)     $signedSchoolsQuery->where('user_id', $spId);
        if ($month) {
            $signedSchoolsQuery->whereYear('invoice_date', substr($month, 0, 4))
                ->whereMonth('invoice_date', substr($month, 5, 2));
        } else {
            $signedSchoolsQuery->whereYear('invoice_date', $year);
        }
        $signedSchools = $signedSchoolsQuery->distinct('customer_id')->count('customer_id');

        // ── TABLE: SP × School breakdown ─────────────────
        $tableRows = Invoice::with(['user:id,username', 'customer:id,name,school_code'])
            ->whereIn('user_id', $teamIds)
            ->whereIn('status', [Invoice::STATUS_APPROVED, Invoice::STATUS_SUBMITTED])
            ->when($spId,     fn($q) => $q->where('user_id', $spId))
            ->when($schoolId, fn($q) => $q->where('customer_id', $schoolId))
            ->when($month, function ($q) use ($month) {
                $q->whereYear('invoice_date',  substr($month, 0, 4))
                    ->whereMonth('invoice_date', substr($month, 5, 2));
            }, function ($q) use ($dateFrom, $dateTo, $year) {
                if ($dateFrom && $dateTo) {
                    $q->whereBetween('invoice_date', [$dateFrom, $dateTo]);
                } else {
                    $q->whereYear('invoice_date', $year);
                }
            })
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
                'pending_po_amount'
            )
            ->orderByDesc('invoice_date')
            ->get();

        // ── CHART: Monthly PO amount trend (12 months) ───
        $chartData = $this->monthlyTrend($teamIds, $year);

        // ── Filter dropdowns ──────────────────────────────
        $teamMembers = User::whereIn('id', $teamIds)
            ->where('id', '!=', $user->id)
            ->orderBy('username')
            ->get(['id', 'username']);

        $schools = Customer::whereHas('invoices', fn($q) => $q->whereIn('user_id', $teamIds))
            ->orderBy('name')
            ->get(['id', 'name', 'school_code']);

        return view('dashboard.manager', compact(
            'totalPoAmount',
            'totalBillingAmount',
            'totalPendingPo',
            'totalCollection',
            'totalOutstanding',
            'signedSchools',
            'tableRows',
            'chartData',
            'teamMembers',
            'schools',
            'spId',
            'schoolId',
            'month',
            'dateFrom',
            'dateTo',
            'year'
        ));
    }

    // ═══════════════════════════════════════════════════
    // SALES PERSON DASHBOARD
    // ═══════════════════════════════════════════════════

    private function salesPersonDashboard(Request $request, User $user)
    {
        $year  = $request->input('year', date('Y'));
        $month = $request->input('month');
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $baseQuery = Invoice::where('user_id', $user->id)
            ->whereIn('status', [Invoice::STATUS_APPROVED, Invoice::STATUS_SUBMITTED]);

        if ($month) {
            $baseQuery->whereYear('invoice_date',  substr($month, 0, 4))
                ->whereMonth('invoice_date', substr($month, 5, 2));
        } elseif ($dateFrom && $dateTo) {
            $baseQuery->whereBetween('invoice_date', [$dateFrom, $dateTo]);
        } else {
            $baseQuery->whereYear('invoice_date', $year);
        }

        // 4 top widgets
        $noOfSchools     = (clone $baseQuery)->distinct('customer_id')->count('customer_id');
        $totalSaleAmount = (clone $baseQuery)->sum('amount');
        $totalCollection = (clone $baseQuery)->sum('collected_amount');
        $totalBillingAmount = (clone $baseQuery)->sum('billing_amount');
        $totalPending    = (clone $baseQuery)->sum('outstanding_amount');

        // School-wise table rows
        $schoolRows = Invoice::with('customer:id,name,school_code')
            ->where('user_id', $user->id)
            ->whereIn('status', [Invoice::STATUS_APPROVED, Invoice::STATUS_SUBMITTED])
            ->when($month, function ($q) use ($month) {
                $q->whereYear('invoice_date',  substr($month, 0, 4))
                    ->whereMonth('invoice_date', substr($month, 5, 2));
            }, function ($q) use ($dateFrom, $dateTo, $year) {
                if ($dateFrom && $dateTo) {
                    $q->whereBetween('invoice_date', [$dateFrom, $dateTo]);
                } else {
                    $q->whereYear('invoice_date', $year);
                }
            })
            ->select(
                'id',
                'po_number',
                'customer_id',
                'amount',
                'billing_amount',
                'collected_amount',
                'outstanding_amount',
                'status'
            )
            ->orderByDesc('invoice_date')
            ->get();

        // Pending POs (draft/submitted) — SP sees their own queue
        $pendingPos = Invoice::where('user_id', $user->id)
            ->whereIn('status', [Invoice::STATUS_DRAFT, Invoice::STATUS_SUBMITTED])
            ->orderByDesc('invoice_date')
            ->get(['id', 'po_number', 'invoice_date', 'amount', 'status']);

        // Chart
        $chartData = $this->monthlyTrend([$user->id], $year);

        return view('dashboard.sales-person', compact(
            'noOfSchools',
            'totalSaleAmount',
            'totalCollection',
            'totalBillingAmount',
            'totalPending',
            'schoolRows',
            'pendingPos',
            'chartData',
            'month',
            'dateFrom',
            'dateTo',
            'year'
        ));
    }

    // ═══════════════════════════════════════════════════
    // ACCOUNTS DASHBOARD
    // ═══════════════════════════════════════════════════

    private function accountsDashboard(Request $request, User $user)
    {
        $teamIds  = $user->teamMemberIds(); // all users
        $spId     = $request->input('sp_id');
        $schoolId = $request->input('school_id');
        $poNumber = $request->input('po_number');
        $month    = $request->input('month');
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');
        $year     = $request->input('year', date('Y'));

        $rows = Invoice::with(['user:id,username', 'customer:id,name,school_code'])
            ->where('status', Invoice::STATUS_APPROVED)
            ->when($spId,     fn($q) => $q->where('user_id', $spId))
            ->when($schoolId, fn($q) => $q->where('customer_id', $schoolId))
            ->when($poNumber, fn($q) => $q->where('po_number', 'like', "%{$poNumber}%"))
            ->when($month, function ($q) use ($month) {
                $q->whereYear('invoice_date',  substr($month, 0, 4))
                    ->whereMonth('invoice_date', substr($month, 5, 2));
            }, function ($q) use ($dateFrom, $dateTo, $year) {
                if ($dateFrom && $dateTo) {
                    $q->whereBetween('invoice_date', [$dateFrom, $dateTo]);
                } else {
                    $q->whereYear('invoice_date', $year);
                }
            })
            ->select(
                'id',
                'po_number',
                'invoice_date',
                'user_id',
                'customer_id',
                'amount',
                'billing_amount',
                'collected_amount',
                'outstanding_amount'
            )
            ->orderByDesc('invoice_date')
            ->get();

        // Totals
        $totalPo          = $rows->sum('amount');
        $totalBilling     = $rows->sum('billing_amount');
        $totalCollected   = $rows->sum('collected_amount');
        $totalOutstanding = $rows->sum('outstanding_amount');

        // Filter dropdowns
        $allSps = User::salesPersons()->active()->orderBy('username')->get(['id', 'username']);
        $schools = Customer::orderBy('name')->get(['id', 'name', 'school_code']);

        return view('dashboard.accounts', compact(
            'rows',
            'totalPo',
            'totalBilling',
            'totalCollected',
            'totalOutstanding',
            'allSps',
            'schools',
            'spId',
            'schoolId',
            'poNumber',
            'month',
            'dateFrom',
            'dateTo',
            'year'
        ));
    }

    private function monthlyTrend(array $userIds, string $year): array
    {
        $rows = Invoice::whereIn('user_id', $userIds)
            ->where('status', Invoice::STATUS_APPROVED)
            ->whereYear('invoice_date', $year)
            ->selectRaw('MONTH(invoice_date) as month, SUM(amount) as total_po, SUM(collected_amount) as total_collected')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $labels = [];
        $poData = [];
        $collectionData = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[]        = date('M', mktime(0, 0, 0, $m, 1));
            $poData[]        = (float) ($rows[$m]->total_po        ?? 0);
            $collectionData[] = (float) ($rows[$m]->total_collected ?? 0);
        }

        return compact('labels', 'poData', 'collectionData');
    }

    private function adminDashboard(Request $request, User $user)
    {
        $month        = $request->input('month');
        $dateFrom     = $request->input('date_from');
        $dateTo       = $request->input('date_to');
        $state        = $request->input('state');
        $leadSourceId = $request->input('lead_source_id');
        $year         = $request->input('year', date('Y'));

        // Base school query with optional filters
        $schoolQuery = Customer::query();
        if ($leadSourceId) $schoolQuery->where('lead_source_id', $leadSourceId);
        if ($state)        $schoolQuery->where('state', $state);

        $totalSchools      = $schoolQuery->count();
        $schoolsThisMonth  = (clone $schoolQuery)
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at',  date('Y'))
            ->count();

        // Approved PO financials
        $poQuery = Invoice::where('status', Invoice::STATUS_APPROVED);
        if ($month) {
            $poQuery->whereYear('invoice_date',  substr($month, 0, 4))
                ->whereMonth('invoice_date', substr($month, 5, 2));
        } elseif ($dateFrom && $dateTo) {
            $poQuery->whereBetween('invoice_date', [$dateFrom, $dateTo]);
        } else {
            $poQuery->whereYear('invoice_date', $year);
        }
        if ($state) {
            $poQuery->whereHas('customer', fn($q) => $q->where('state', $state));
        }
        if ($leadSourceId) {
            $poQuery->whereHas('customer', fn($q) => $q->where('lead_source_id', $leadSourceId));
        }

        $signedSchools   = (clone $poQuery)->distinct('customer_id')->count('customer_id');
        $totalRevenue    = (clone $poQuery)->sum('amount');
        $totalOutstanding = (clone $poQuery)->sum('outstanding_amount');
        $totalPos        = (clone $poQuery)->count();
        $totalSps        = User::salesPersons()->active()->count();

        // Lead source breakdown for pie chart
        $leadBreakdown = Customer::with('leadSource')
            ->selectRaw('lead_source_id, COUNT(*) as count')
            ->groupBy('lead_source_id')
            ->get();

        $leadChartData = [
            'labels' => $leadBreakdown->map(fn($r) => $r->leadSource?->name ?? 'Unknown')->toArray(),
            'data'   => $leadBreakdown->pluck('count')->toArray(),
        ];

        // Monthly trend
        $chartData = $this->monthlyTrend(
            User::pluck('id')->toArray(),
            $year
        );

        // Recent users for table
        $recentUsers = User::with('role', 'reportiveTo')
            ->active()
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('dashboard.admin', compact(
            'signedSchools',
            'totalRevenue',
            'totalOutstanding',
            'totalSchools',
            'schoolsThisMonth',
            'totalPos',
            'totalSps',
            'leadChartData',
            'chartData',
            'recentUsers',
            'month',
            'dateFrom',
            'dateTo',
            'state',
            'leadSourceId',
            'year'
        ));
    }
}
