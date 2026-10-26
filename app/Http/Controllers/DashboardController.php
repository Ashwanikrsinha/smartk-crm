<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Target;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = auth()->user();

        // ── Role routing (order matters) ──────────────────────
        if ($user->isAdmin() || $user->isBusinessManager()) {
            return $this->adminDashboard($request, $user);
        }

        if ($user->isSalesManager()) {
            return $this->managerDashboard($request, $user);
        }

        if ($user->isSalesPerson()) {
            return $this->salesPersonDashboard($request, $user);
        }

        if ($user->isAccounts()) {
            return $this->accountsDashboard($request, $user);
        }

        if ($user->isWarehouse()) {
            return $this->warehouseDashboard($request, $user);
        }

        if ($user->isMarketing()) {
            return $this->marketingDashboard($request, $user);
        }

        return view('dashboard.default');
    }

    // ═══════════════════════════════════════════════════
    // WAREHOUSE DASHBOARD
    // ═══════════════════════════════════════════════════

    private function warehouseDashboard(Request $request, User $user)
    {
        $sessionInput   = $request->input('session', $request->input('year'));
        $sessionRange   = $this->sessionDateRange($sessionInput);
        $session        = $sessionRange['session'];
        $sessions       = $this->availableSessions();
        $year           = $sessionRange['start_year'];
        $month          = $request->input('month');
        $dateFrom       = $request->input('date_from');
        $dateTo         = $request->input('date_to');
        $duedateFrom    = $request->input('due_date_from');
        $duedateTo      = $request->input('due_date_to');
        $productType    = $request->input('product_type', 'all');
        $productId      = $request->input('product_id', 'all');
        $dispatchStatus = $request->input('dispatch_status', 'pending');

        $totalOrderedQty = 0;
        $totalDoneQty    = 0;

        $query = Invoice::with([
            'customer:id,name,school_code',
            'invoiceItems.product.category',
            'invoiceItems.unit',
            'dispatches.items',
        ])->where('status', Invoice::STATUS_APPROVED);

        if ($month) {
            $query->whereYear('invoice_date', substr($month, 0, 4))
                ->whereMonth('invoice_date', substr($month, 5, 2));
        } elseif ($dateFrom && $dateTo) {
            $query->whereBetween('invoice_date', [$dateFrom, $dateTo]);
        } else {
            $query->whereBetween('invoice_date', [$sessionRange['start_date'], $sessionRange['end_date']]);
        }

        if ($duedateFrom && $duedateTo) {
            $query->whereBetween('delivery_due_date', [$duedateFrom, $duedateTo]);
        }

        if ($productType !== 'all') {
            $query->whereHas('invoiceItems.product', fn($q) => $q->where('category_id', $productType));
        }

        if ($productId !== 'all') {
            $query->whereHas('invoiceItems', fn($q) => $q->where('product_id', $productId));
        }

        $allRows = $query->orderBy('delivery_due_date', 'asc')->get();

        foreach ($allRows as $invoice) {
            $dispatched = \App\Models\DispatchItem::whereHas(
                'dispatch',
                fn($q) => $q->where('invoice_id', $invoice->id)
            )
                ->selectRaw('invoice_item_id, SUM(quantity_dispatched) as done')
                ->groupBy('invoice_item_id')
                ->get()
                ->keyBy('invoice_item_id');

            foreach ($invoice->invoiceItems as $key => $item) {
                $item->done_qty      = (float) ($dispatched[$item->id]->done ?? 0);
                $item->remaining_qty = max(round((float) $item->quantity - $item->done_qty, 3), 0);

                if ($productType !== 'all' && optional($item->product)->category_id != $productType) {
                    unset($invoice->invoiceItems[$key]);
                    continue;
                }

                if ($productId !== 'all' && $item->product_id != $productId) {
                    unset($invoice->invoiceItems[$key]);
                    continue;
                }

                $totalOrderedQty += (float) $item->quantity;
                $totalDoneQty    += $item->done_qty;
            }

            $invoice->setRelation('invoiceItems', $invoice->invoiceItems->values());

            $invoice->is_fully_dispatched =
                $invoice->invoiceItems->isEmpty() ||
                $invoice->invoiceItems->every(fn($i) => $i->remaining_qty <= 0);
        }

        $totalPendingQty = max($totalOrderedQty - $totalDoneQty, 0);

        if ($dispatchStatus === 'all') {
            $filteredRows = $allRows;
        } elseif ($dispatchStatus === 'completed') {
            $filteredRows = $allRows->filter(fn($i) => $i->is_fully_dispatched);
        } else {
            $filteredRows = $allRows->reject(fn($i) => $i->is_fully_dispatched);
        }

        $perPage = 15;
        $page    = $request->input('page', 1);
        $rows    = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredRows->forPage($page, $perPage),
            $filteredRows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $products     = Product::all();
        $productTypes = Category::all();

        $totalProductTypes = collect($allRows)
            ->flatMap(fn($i) => $i->invoiceItems)
            ->pluck('product.category_id')->unique()->count();

        $totalProducts = collect($allRows)
            ->flatMap(fn($i) => $i->invoiceItems)
            ->pluck('product_id')->unique()->count();

        $totalDispatchedProducts = collect($allRows)
            ->flatMap(fn($i) => $i->invoiceItems)
            ->where('done_qty', '>', 0)
            ->pluck('product_id')->unique()->count();

        $totalPendingProducts = collect($allRows)
            ->flatMap(fn($i) => $i->invoiceItems)
            ->where('remaining_qty', '>', 0)
            ->pluck('product_id')->unique()->count();

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
            'session',
            'sessions',
            'month',
            'dateFrom',
            'dateTo',
            'productType',
            'productId',
            'products',
            'productTypes',
            'duedateFrom',
            'duedateTo',
            'dispatchStatus'
        ));
    }

    // ═══════════════════════════════════════════════════
    // MARKETING DASHBOARD
    // ═══════════════════════════════════════════════════

    private function marketingDashboard(Request $request, User $user)
    {
        $sessionInput   = $request->input('session', $request->input('year'));
        $sessionRange   = $this->sessionDateRange($sessionInput);
        $session        = $sessionRange['session'];
        $sessions       = $this->availableSessions();
        $year           = $sessionRange['start_year'];
        $month          = $request->input('month');
        $dateFrom       = $request->input('date_from');
        $dateTo         = $request->input('date_to');
        $state          = $request->input('state');
        $leadSourceId   = $request->input('lead_source_id');

        // Schools (basic data only)
        $schoolQuery = Customer::query();
        if ($leadSourceId) $schoolQuery->where('lead_source_id', $leadSourceId);
        if ($state)        $schoolQuery->where('state', $state);

        $totalSchools = $schoolQuery->count();

        $schools = (clone $schoolQuery)
            ->with('leadSource')
            ->orderBy('name')
            ->get(['id', 'name', 'school_code', 'city', 'state', 'phone_number', 'lead_source_id']);

        // PO query — approved only, NO financial details beyond amount
        $poQuery = Invoice::with('customer:id,name,school_code')
            ->whereIn('status', [Invoice::STATUS_APPROVED, Invoice::STATUS_SUBMITTED]);

        if ($month) {
            $poQuery->whereYear('invoice_date', substr($month, 0, 4))
                ->whereMonth('invoice_date', substr($month, 5, 2));
        } elseif ($dateFrom && $dateTo) {
            $poQuery->whereBetween('invoice_date', [$dateFrom, $dateTo]);
        } else {
            $poQuery->whereBetween('invoice_date', [$sessionRange['start_date'], $sessionRange['end_date']]);
        }

        if ($state) {
            $poQuery->whereHas('customer', fn($q) => $q->where('state', $state));
        }
        if ($leadSourceId) {
            $poQuery->whereHas('customer', fn($q) => $q->where('lead_source_id', $leadSourceId));
        }

        // Only pull safe columns — no collected_amount, no outstanding_amount
        $pos = (clone $poQuery)
            ->select('id', 'po_number', 'invoice_date', 'customer_id', 'amount', 'status')
            ->orderByDesc('invoice_date')
            ->get();

        $totalPos      = $pos->count();
        $signedSchools = (clone $poQuery)
            ->where('status', Invoice::STATUS_APPROVED)
            ->distinct('customer_id')->count('customer_id');

        $totalRevenue = $pos->where('status', Invoice::STATUS_APPROVED)->sum('amount');

        // Lead source pie chart
        $leadBreakdown = Customer::with('leadSource')
            ->selectRaw('lead_source_id, COUNT(*) as count')
            ->groupBy('lead_source_id')
            ->get();

        $leadChartData = [
            'labels' => $leadBreakdown->map(fn($r) => $r->leadSource?->name ?? 'Unknown')->toArray(),
            'data'   => $leadBreakdown->pluck('count')->toArray(),
        ];

        // PO monthly trend (amount only — no collection) across session
        $chartData = $this->marketingMonthlyTrend($sessionRange);

        return view('dashboard.marketing', compact(
            'totalSchools',
            'signedSchools',
            'totalPos',
            'totalRevenue',
            'schools',
            'pos',
            'leadChartData',
            'chartData',
            'month',
            'dateFrom',
            'dateTo',
            'state',
            'leadSourceId',
            'year',
            'session',
            'sessions'
        ));
    }

    // ═══════════════════════════════════════════════════
    // SALES MANAGER DASHBOARD
    // ═══════════════════════════════════════════════════

    private function managerDashboard(Request $request, User $user)
    {
        $teamIds      = $user->teamMemberIds();
        $spId         = $request->input('sp_id');
        $schoolId     = $request->input('school_id');
        $month        = $request->input('month');
        $dateFrom     = $request->input('date_from');
        $dateTo       = $request->input('date_to');
        $sessionInput = $request->input('session', $request->input('year'));
        $sessionRange = $this->sessionDateRange($sessionInput);
        $session      = $sessionRange['session'];
        $sessions     = $this->availableSessions();
        $year         = $sessionRange['start_year'];

        $baseQuery = Invoice::whereIn('user_id', $teamIds)
            ->whereIn('status', [Invoice::STATUS_APPROVED, Invoice::STATUS_SUBMITTED]);

        if ($spId)     $baseQuery->where('user_id', $spId);
        if ($schoolId) $baseQuery->where('customer_id', $schoolId);
        if ($month) {
            $baseQuery->whereYear('invoice_date', substr($month, 0, 4))
                ->whereMonth('invoice_date', substr($month, 5, 2));
        } elseif ($dateFrom && $dateTo) {
            $baseQuery->whereBetween('invoice_date', [$dateFrom, $dateTo]);
        } else {
            $baseQuery->whereBetween('invoice_date', [$sessionRange['start_date'], $sessionRange['end_date']]);
        }

        $currentPoAmount      = (clone $baseQuery)->sum('amount');
        $currentBillingAmount = (clone $baseQuery)->sum('billing_amount');
        $currentPendingPo     = $currentPoAmount - $currentBillingAmount;
        $currentCollection    = (clone $baseQuery)->sum('collected_amount');
        $currentOutstanding   = (clone $baseQuery)->sum('outstanding_amount');

        // Opening balance carry-forward for manager dashboard (all SPs in manager's team)
        $obQuery = \App\Models\SchoolOpeningBalance::with([
            'salesPerson:id,username,emp_code',
            'customer:id,name,school_code'
        ])
            ->whereIn('user_id', $teamIds)
            ->forSession($year)
            ->when($spId, fn($q) => $q->where('user_id', $spId))
            ->when($schoolId, fn($q) => $q->where('customer_id', $schoolId));

        $totalOpeningAmount      = (float) $obQuery->sum('opening_amount');
        $totalOpeningBilled      = (float) $obQuery->sum('opening_billed');
        $totalOpeningCollected   = (float) $obQuery->sum('opening_collected');
        $totalOpeningOutstanding = (float) $obQuery->sum('opening_outstanding');
        $openingBalanceRows      = $obQuery->get();

        // Overall summary totals
        $totalPoAmount      = $currentPoAmount;
        $totalBillingAmount = $currentBillingAmount;
        $totalPendingPo     = $currentPendingPo;
        $totalCollection    = $currentCollection + $totalOpeningCollected;
        $totalOutstanding   = $currentOutstanding + $totalOpeningOutstanding;

        $signedSchoolsQuery = Invoice::whereIn('user_id', $teamIds)
            ->where('status', Invoice::STATUS_APPROVED);
        if ($spId) $signedSchoolsQuery->where('user_id', $spId);
        if ($month) {
            $signedSchoolsQuery->whereYear('invoice_date', substr($month, 0, 4))
                ->whereMonth('invoice_date', substr($month, 5, 2));
        } elseif ($dateFrom && $dateTo) {
            $signedSchoolsQuery->whereBetween('invoice_date', [$dateFrom, $dateTo]);
        } else {
            $signedSchoolsQuery->whereBetween('invoice_date', [$sessionRange['start_date'], $sessionRange['end_date']]);
        }
        $signedSchools = $signedSchoolsQuery->distinct('customer_id')->count('customer_id');

        $tableRows = Invoice::with(['user:id,username', 'customer:id,name,school_code'])
            ->whereIn('user_id', $teamIds)
            ->whereIn('status', [Invoice::STATUS_APPROVED, Invoice::STATUS_SUBMITTED])
            ->when($spId,     fn($q) => $q->where('user_id', $spId))
            ->when($schoolId, fn($q) => $q->where('customer_id', $schoolId))
            ->when(
                $month,
                fn($q) => $q->whereYear('invoice_date', substr($month, 0, 4))
                    ->whereMonth('invoice_date', substr($month, 5, 2)),
                fn($q) => $dateFrom && $dateTo
                    ? $q->whereBetween('invoice_date', [$dateFrom, $dateTo])
                    : $q->whereBetween('invoice_date', [$sessionRange['start_date'], $sessionRange['end_date']])
            )
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

        $chartData   = $this->monthlyTrend($teamIds, $sessionRange);
        $teamMembers = User::whereIn('id', $teamIds)
            ->orderBy('username')->get(['id', 'username']);
        $schools     = Customer::whereHas('invoices', fn($q) => $q->whereIn('user_id', $teamIds))
            ->orWhereHas('openingBalances', fn($q) => $q->whereIn('user_id', $teamIds))
            ->orderBy('name')->get(['id', 'name', 'school_code']);

        // Target vs Achievement Calculation (aligned with session/dashboard filter)
        $targetUsers = $spId ? [$spId] : $teamIds;

        $targetQuery = Target::whereIn('user_id', $targetUsers);
        $invoiceAchievedQuery = Invoice::whereIn('user_id', $targetUsers)
            ->where('status', Invoice::STATUS_APPROVED);

        if ($month) {
            $tYear  = (int) substr($month, 0, 4);
            $tMonth = (int) substr($month, 5, 2);
            $targetQuery->where('year', $tYear)->where('month', $tMonth);
            $invoiceAchievedQuery->whereYear('invoice_date', $tYear)->whereMonth('invoice_date', $tMonth);
            $targetMonthLabel = date('F Y', mktime(0, 0, 0, $tMonth, 1, $tYear));
        } elseif ($dateFrom && $dateTo) {
            $startDate  = strtotime($dateFrom);
            $endDate    = strtotime($dateTo);
            $startYear  = (int) date('Y', $startDate);
            $startMonth = (int) date('m', $startDate);
            $endYear    = (int) date('Y', $endDate);
            $endMonth   = (int) date('m', $endDate);

            $targetQuery->where(function ($q) use ($startYear, $startMonth, $endYear, $endMonth) {
                if ($startYear === $endYear) {
                    $q->where('year', $startYear)->whereBetween('month', [$startMonth, $endMonth]);
                } else {
                    $q->where(function ($sq) use ($startYear, $startMonth) {
                        $sq->where('year', $startYear)->where('month', '>=', $startMonth);
                    })->orWhere(function ($sq) use ($endYear, $endMonth) {
                        $sq->where('year', $endYear)->where('month', '<=', $endMonth);
                    })->orWhere(function ($sq) use ($startYear, $endYear) {
                        $sq->where('year', '>', $startYear)->where('year', '<', $endYear);
                    });
                }
            });

            $invoiceAchievedQuery->whereBetween('invoice_date', [$dateFrom, $dateTo]);
            $targetMonthLabel = date('d M Y', $startDate) . ' – ' . date('d M Y', $endDate);
        } else {
            // Session: July startYear to June endYear
            $startYear = $sessionRange['start_year'];
            $endYear   = $sessionRange['end_year'];

            $targetQuery->where(function ($q) use ($startYear, $endYear) {
                $q->where(function ($sq) use ($startYear) {
                    $sq->where('year', $startYear)->where('month', '>=', 7);
                })->orWhere(function ($sq) use ($endYear) {
                    $sq->where('year', $endYear)->where('month', '<=', 6);
                });
            });

            $invoiceAchievedQuery->whereBetween('invoice_date', [$sessionRange['start_date'], $sessionRange['end_date']]);
            $targetMonthLabel = "Session {$session}";
        }

        $targetAmount = (float) $targetQuery->sum('target_amount');
        if ($month) {
            $tYear  = (int) substr($month, 0, 4);
            $tMonth = (int) substr($month, 5, 2);
            $targetAchieved = Target::calculateAchievedForPeriod($targetUsers, $tMonth, $tYear);
        } elseif ($dateFrom && $dateTo) {
            $targetAchieved = Target::calculateAchievedForPeriod($targetUsers, null, null, $dateFrom, $dateTo);
        } else {
            $targetAchieved = Target::calculateAchievedForPeriod($targetUsers, null, null, null, null, $sessionRange);
        }

        $targetPct = $targetAmount > 0 ? round(($targetAchieved / $targetAmount) * 100) : 0;
        $targetPctColor = $targetPct >= 100 ? 'success' : ($targetPct >= 60 ? 'warning' : 'danger');

        return view('dashboard.manager', compact(
            'totalPoAmount',
            'currentPoAmount',
            'totalBillingAmount',
            'totalPendingPo',
            'totalCollection',
            'totalOutstanding',
            'totalOpeningAmount',
            'totalOpeningBilled',
            'totalOpeningCollected',
            'totalOpeningOutstanding',
            'openingBalanceRows',
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
            'year',
            'session',
            'sessions',
            'targetAmount',
            'targetAchieved',
            'targetPct',
            'targetPctColor',
            'targetMonthLabel'
        ));
    }

    // ═══════════════════════════════════════════════════
    // SALES PERSON DASHBOARD
    // ═══════════════════════════════════════════════════

    private function salesPersonDashboard(Request $request, User $user)
    {
        $sessionInput   = $request->input('session', $request->input('year'));
        $sessionRange   = $this->sessionDateRange($sessionInput);
        $session        = $sessionRange['session'];
        $sessions       = $this->availableSessions();
        $year           = $sessionRange['start_year'];
        $month          = $request->input('month');
        $dateFrom       = $request->input('date_from');
        $dateTo         = $request->input('date_to');

        $baseQuery = Invoice::where('user_id', $user->id)
            ->whereIn('status', [Invoice::STATUS_APPROVED, Invoice::STATUS_SUBMITTED]);

        if ($month) {
            $baseQuery->whereYear('invoice_date', substr($month, 0, 4))
                ->whereMonth('invoice_date', substr($month, 5, 2));
        } elseif ($dateFrom && $dateTo) {
            $baseQuery->whereBetween('invoice_date', [$dateFrom, $dateTo]);
        } else {
            $baseQuery->whereBetween('invoice_date', [$sessionRange['start_date'], $sessionRange['end_date']]);
        }

        // Current session PO metrics
        $currentPoAmount      = (clone $baseQuery)->sum('amount');
        $currentBillingAmount = (clone $baseQuery)->sum('billing_amount');
        $currentCollection    = (clone $baseQuery)->sum('collected_amount');
        $currentOutstanding   = (clone $baseQuery)->sum('outstanding_amount');

        // Opening balance for this SP in this session
        $obQuery = \App\Models\SchoolOpeningBalance::with('customer:id,name,school_code')
            ->where('user_id', $user->id)
            ->forSession($year);

        $totalOpeningAmount      = (float) $obQuery->sum('opening_amount');
        $totalOpeningBilled      = (float) $obQuery->sum('opening_billed');
        $totalOpeningCollected   = (float) $obQuery->sum('opening_collected');
        $totalOpeningOutstanding = (float) $obQuery->sum('opening_outstanding');
        $openingRows             = $obQuery->get();

        // Distinct schools count (signed in session + schools with opening balance)
        $poSchoolIds = (clone $baseQuery)->distinct()->pluck('customer_id')->toArray();
        $obSchoolIds = $openingRows->pluck('customer_id')->toArray();
        $noOfSchools = count(array_unique(array_merge($poSchoolIds, $obSchoolIds)));

        // Overall summary totals
        $totalSaleAmount    = $currentPoAmount;
        $totalBillingAmount = $currentBillingAmount;
        $totalCollection    = $currentCollection + $totalOpeningCollected;
        $totalPending       = $currentOutstanding + $totalOpeningOutstanding;

        $schoolRows = Invoice::with('customer:id,name,school_code')
            ->where('user_id', $user->id)
            ->whereIn('status', [Invoice::STATUS_APPROVED, Invoice::STATUS_SUBMITTED])
            ->when(
                $month,
                fn($q) => $q->whereYear('invoice_date', substr($month, 0, 4))
                    ->whereMonth('invoice_date', substr($month, 5, 2)),
                fn($q) => $dateFrom && $dateTo
                    ? $q->whereBetween('invoice_date', [$dateFrom, $dateTo])
                    : $q->whereBetween('invoice_date', [$sessionRange['start_date'], $sessionRange['end_date']])
            )
            ->select('id', 'po_number', 'customer_id', 'amount', 'billing_amount', 'collected_amount', 'outstanding_amount', 'status')
            ->orderByDesc('invoice_date')
            ->get();

        $pendingPos = Invoice::where('user_id', $user->id)
            ->whereIn('status', [Invoice::STATUS_DRAFT, Invoice::STATUS_SUBMITTED])
            ->whereBetween('invoice_date', [$sessionRange['start_date'], $sessionRange['end_date']])
            ->orderByDesc('invoice_date')
            ->get(['id', 'po_number', 'invoice_date', 'amount', 'status']);

        $chartData = $this->monthlyTrend([$user->id], $sessionRange);

        return view('dashboard.sales-person', compact(
            'noOfSchools',
            'totalSaleAmount',
            'currentPoAmount',
            'totalCollection',
            'totalBillingAmount',
            'totalPending',
            'totalOpeningAmount',
            'totalOpeningBilled',
            'totalOpeningCollected',
            'totalOpeningOutstanding',
            'openingRows',
            'schoolRows',
            'pendingPos',
            'chartData',
            'month',
            'dateFrom',
            'dateTo',
            'year',
            'session',
            'sessions'
        ));
    }

    // ═══════════════════════════════════════════════════
    // ACCOUNTS DASHBOARD
    // ═══════════════════════════════════════════════════

    private function accountsDashboard(Request $request, User $user)
    {
        $sessionInput = $request->input('session', $request->input('year'));
        $sessionRange = $this->sessionDateRange($sessionInput);
        $session      = $sessionRange['session'];
        $sessions     = $this->availableSessions();
        $year         = $sessionRange['start_year'];
        $spId         = $request->input('sp_id');
        $schoolId     = $request->input('school_id');
        $poNumber     = $request->input('po_number');
        $month        = $request->input('month');
        $dateFrom     = $request->input('date_from');
        $dateTo       = $request->input('date_to');

        $rows = Invoice::with(['user:id,username', 'customer:id,name,school_code'])
            ->where('status', Invoice::STATUS_APPROVED)
            ->when($spId,     fn($q) => $q->where('user_id', $spId))
            ->when($schoolId, fn($q) => $q->where('customer_id', $schoolId))
            ->when($poNumber, fn($q) => $q->where('po_number', 'like', "%{$poNumber}%"))
            ->when(
                $month,
                fn($q) => $q->whereYear('invoice_date', substr($month, 0, 4))
                    ->whereMonth('invoice_date', substr($month, 5, 2)),
                fn($q) => $dateFrom && $dateTo
                    ? $q->whereBetween('invoice_date', [$dateFrom, $dateTo])
                    : $q->whereBetween('invoice_date', [$sessionRange['start_date'], $sessionRange['end_date']])
            )
            ->select(
                'id',
                'po_number',
                'invoice_date',
                'user_id',
                'customer_id',
                'amount',
                'gst_amount',
                'billing_amount',
                'collected_amount',
                'outstanding_amount'
            )
            ->orderByDesc('invoice_date')
            ->get();

        $currentPo          = $rows->sum('amount');
        $currentBilling     = $rows->sum('billing_amount');
        $currentCollected   = $rows->sum('collected_amount');
        $currentOutstanding = $rows->sum('outstanding_amount');

        // Add opening balance carry-forward
        $obQuery = \App\Models\SchoolOpeningBalance::with(['salesPerson:id,username,emp_code', 'customer:id,name,school_code'])
            ->forSession($year)
            ->when($spId, fn($q) => $q->where('user_id', $spId))
            ->when($schoolId, fn($q) => $q->where('customer_id', $schoolId));

        $totalOpeningAmount      = (float) $obQuery->sum('opening_amount');
        $totalOpeningBilled      = (float) $obQuery->sum('opening_billed');
        $totalOpeningCollected   = (float) $obQuery->sum('opening_collected');
        $totalOpeningOutstanding = (float) $obQuery->sum('opening_outstanding');
        $openingBalanceRows      = $obQuery->get();

        $totalPo          = $currentPo;
        $totalBilling     = $currentBilling;
        $totalCollected   = $currentCollected + $totalOpeningCollected;
        $totalOutstanding = $currentOutstanding + $totalOpeningOutstanding;

        $allSps  = User::salesPersons()->active()->orderBy('username')->get(['id', 'username']);
        $schools = Customer::orderBy('name')->get(['id', 'name', 'school_code']);

        return view('dashboard.accounts', compact(
            'rows',
            'openingBalanceRows',
            'totalOpeningAmount',
            'totalOpeningBilled',
            'totalOpeningCollected',
            'totalOpeningOutstanding',
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
            'year',
            'session',
            'sessions'
        ));
    }

    // ═══════════════════════════════════════════════════
    // ADMIN DASHBOARD
    // ═══════════════════════════════════════════════════

    private function adminDashboard(Request $request, User $user)
    {
        $sessionInput = $request->input('session', $request->input('year'));
        $sessionRange = $this->sessionDateRange($sessionInput);
        $session      = $sessionRange['session'];
        $sessions     = $this->availableSessions();
        $year         = $sessionRange['start_year'];
        $month        = $request->input('month');
        $dateFrom     = $request->input('date_from');
        $dateTo       = $request->input('date_to');
        $state        = $request->input('state');
        $leadSourceId = $request->input('lead_source_id');

        // Base school query with optional filters
        $schoolQuery = Customer::query();
        if ($leadSourceId) $schoolQuery->where('lead_source_id', $leadSourceId);
        if ($state)        $schoolQuery->where('state', $state);

        $totalSchools     = $schoolQuery->count();
        $schoolsThisMonth = (clone $schoolQuery)
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at',  date('Y'))
            ->count();

        // Approved PO financials in session
        $poQuery = Invoice::where('status', Invoice::STATUS_APPROVED);
        if ($month) {
            $poQuery->whereYear('invoice_date',  substr($month, 0, 4))
                ->whereMonth('invoice_date', substr($month, 5, 2));
        } elseif ($dateFrom && $dateTo) {
            $poQuery->whereBetween('invoice_date', [$dateFrom, $dateTo]);
        } else {
            $poQuery->whereBetween('invoice_date', [$sessionRange['start_date'], $sessionRange['end_date']]);
        }
        if ($state)        $poQuery->whereHas('customer', fn($q) => $q->where('state', $state));
        if ($leadSourceId) $poQuery->whereHas('customer', fn($q) => $q->where('lead_source_id', $leadSourceId));

        $signedSchools    = (clone $poQuery)->distinct('customer_id')->count('customer_id');
        $currentRevenue   = (clone $poQuery)->sum('amount');
        $currentOutstanding = (clone $poQuery)->sum('outstanding_amount');
        $currentCollection  = (clone $poQuery)->sum('collected_amount');
        $totalPos         = (clone $poQuery)->count();
        $totalSps         = User::salesPersons()->active()->count();

        // Add opening balance carry-forward
        $obQuery = \App\Models\SchoolOpeningBalance::with(['salesPerson:id,username,emp_code', 'customer:id,name,school_code'])
            ->forSession($year)
            ->when($state, fn($q) => $q->whereHas('customer', fn($sq) => $sq->where('state', $state)))
            ->when($leadSourceId, fn($q) => $q->whereHas('customer', fn($sq) => $sq->where('lead_source_id', $leadSourceId)));

        $totalOpeningAmount      = (float) $obQuery->sum('opening_amount');
        $totalOpeningBilled      = (float) $obQuery->sum('opening_billed');
        $totalOpeningCollected   = (float) $obQuery->sum('opening_collected');
        $totalOpeningOutstanding = (float) $obQuery->sum('opening_outstanding');
        $openingBalanceRows      = $obQuery->get();

        $totalRevenue     = $currentRevenue;
        $totalCollection  = $currentCollection + $totalOpeningCollected;
        $totalOutstanding = $currentOutstanding + $totalOpeningOutstanding;

        $leadBreakdown = Customer::with('leadSource')
            ->selectRaw('lead_source_id, COUNT(*) as count')
            ->groupBy('lead_source_id')->get();

        $leadChartData = [
            'labels' => $leadBreakdown->map(fn($r) => $r->leadSource?->name ?? 'Unknown')->toArray(),
            'data'   => $leadBreakdown->pluck('count')->toArray(),
        ];

        $chartData = $this->monthlyTrend(User::pluck('id')->toArray(), $sessionRange);

        $recentUsers = User::with('role', 'reportiveTo')->active()
            ->orderByDesc('id')->limit(8)->get();

        return view('dashboard.admin', compact(
            'signedSchools',
            'totalRevenue',
            'totalCollection',
            'totalOutstanding',
            'totalOpeningAmount',
            'totalOpeningBilled',
            'totalOpeningCollected',
            'totalOpeningOutstanding',
            'openingBalanceRows',
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
            'year',
            'session',
            'sessions'
        ));
    }
    /**
     * Get current school session string (e.g. "2026-2027" for July 2026 - June 2027).
     */
    private function currentSession(): string
    {
        $month = (int) date('n');
        $year  = (int) date('Y');
        $startYear = ($month >= 7) ? $year : $year - 1;
        return $startYear . '-' . ($startYear + 1);
    }

    /**
     * Parse session string into start/end dates and years.
     */
    private function sessionDateRange(?string $sessionInput): array
    {
        if (empty($sessionInput)) {
            $session = $this->currentSession();
        } elseif (preg_match('/^(\d{4})-(\d{4})$/', $sessionInput, $m)) {
            $session = $sessionInput;
        } elseif (preg_match('/^\d{4}$/', $sessionInput)) {
            $y = (int) $sessionInput;
            $session = $y . '-' . ($y + 1);
        } else {
            $session = $this->currentSession();
        }

        [$startYear, $endYear] = explode('-', $session);
        $startYear = (int) $startYear;
        $endYear   = (int) $endYear;

        return [
            'session'    => $session,
            'start_date' => sprintf('%04d-07-01', $startYear),
            'end_date'   => sprintf('%04d-06-30', $endYear),
            'start_year' => $startYear,
            'end_year'   => $endYear,
        ];
    }

    /**
     * Available sessions for dropdown filter.
     */
    private function availableSessions(): array
    {
        $currStart = ((int) date('n') >= 7) ? (int) date('Y') : (int) date('Y') - 1;
        $sessions = [];
        for ($y = $currStart + 1; $y >= $currStart - 4; $y--) {
            $sessions[] = $y . '-' . ($y + 1);
        }
        return $sessions;
    }

    /** PO + Collection monthly trend (for Admin, Manager, SP) across July to June */
    private function monthlyTrend(array $userIds, array $sessionRange): array
    {
        $startYear = $sessionRange['start_year'];
        $endYear   = $sessionRange['end_year'];

        $monthSeq = [];
        for ($m = 7; $m <= 12; $m++) {
            $monthSeq[] = ['year' => $startYear, 'month' => $m, 'label' => date('M', mktime(0, 0, 0, $m, 1)) . " '" . substr((string) $startYear, 2)];
        }
        for ($m = 1; $m <= 6; $m++) {
            $monthSeq[] = ['year' => $endYear, 'month' => $m, 'label' => date('M', mktime(0, 0, 0, $m, 1)) . " '" . substr((string) $endYear, 2)];
        }

        $rows = Invoice::whereIn('user_id', $userIds)
            ->where('status', Invoice::STATUS_APPROVED)
            ->whereBetween('invoice_date', [$sessionRange['start_date'], $sessionRange['end_date']])
            ->selectRaw('YEAR(invoice_date) as yr, MONTH(invoice_date) as mo, SUM(amount) as total_po, SUM(collected_amount) as total_collected')
            ->groupBy('yr', 'mo')
            ->get()
            ->keyBy(fn($r) => $r->yr . '-' . str_pad((string) $r->mo, 2, '0', STR_PAD_LEFT));

        $obRows = \App\Models\OpeningBalanceCollection::whereHas('openingBalance', fn($q) => $q->whereIn('user_id', $userIds))
            ->where('payment_mode', '!=', 'return')
            ->whereBetween('collected_at', [$sessionRange['start_date'], $sessionRange['end_date']])
            ->selectRaw('YEAR(collected_at) as yr, MONTH(collected_at) as mo, SUM(collected_amount) as total_ob_collected')
            ->groupBy('yr', 'mo')
            ->get()
            ->keyBy(fn($r) => $r->yr . '-' . str_pad((string) $r->mo, 2, '0', STR_PAD_LEFT));

        $labels = $poData = $collectionData = [];
        foreach ($monthSeq as $seq) {
            $key = $seq['year'] . '-' . str_pad((string) $seq['month'], 2, '0', STR_PAD_LEFT);
            $labels[]         = $seq['label'];
            $poData[]         = (float) ($rows[$key]->total_po        ?? 0);
            $collectionData[] = (float) (($rows[$key]->total_collected ?? 0) + ($obRows[$key]->total_ob_collected ?? 0));
        }

        return compact('labels', 'poData', 'collectionData');
    }

    /** PO-only monthly trend (for Marketing — no collection data) across July to June */
    private function marketingMonthlyTrend(array $sessionRange): array
    {
        $startYear = $sessionRange['start_year'];
        $endYear   = $sessionRange['end_year'];

        $monthSeq = [];
        for ($m = 7; $m <= 12; $m++) {
            $monthSeq[] = ['year' => $startYear, 'month' => $m, 'label' => date('M', mktime(0, 0, 0, $m, 1)) . " '" . substr((string) $startYear, 2)];
        }
        for ($m = 1; $m <= 6; $m++) {
            $monthSeq[] = ['year' => $endYear, 'month' => $m, 'label' => date('M', mktime(0, 0, 0, $m, 1)) . " '" . substr((string) $endYear, 2)];
        }

        $rows = Invoice::where('status', Invoice::STATUS_APPROVED)
            ->whereBetween('invoice_date', [$sessionRange['start_date'], $sessionRange['end_date']])
            ->selectRaw('YEAR(invoice_date) as yr, MONTH(invoice_date) as mo, SUM(amount) as total_po')
            ->groupBy('yr', 'mo')
            ->get()
            ->keyBy(fn($r) => $r->yr . '-' . str_pad((string) $r->mo, 2, '0', STR_PAD_LEFT));

        $labels = $poData = [];
        foreach ($monthSeq as $seq) {
            $key = $seq['year'] . '-' . str_pad((string) $seq['month'], 2, '0', STR_PAD_LEFT);
            $labels[] = $seq['label'];
            $poData[] = (float) ($rows[$key]->total_po ?? 0);
        }

        // collectionData intentionally omitted for Marketing
        $collectionData = array_fill(0, 12, null);

        return compact('labels', 'poData', 'collectionData');
    }
}
