@extends('layouts.dashboard')
@section('content')

{{-- ═══ HEADER ═══════════════════════════════════════════ --}}
<header class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0">Sales Manager Dashboard</h5>
        <small class="text-muted">Hello, {{ auth()->user()->username }} — Overview for Session {{ $session }}</small>
    </div>
    @can('export_reports', App\Models\Invoice::class)
    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary">
        <i class="feather icon-bar-chart-2 me-1"></i> Full Reports
    </a>
    @endcan
</header>

{{-- ═══ FILTERS ════════════════════════════════════════════ --}}
<div class="bg-white rounded shadow-sm p-3 mb-4">
    <form method="GET" action="{{ route('dashboard') }}" class="row g-2 align-items-end">

        <div class="col-lg-2 col-md-4">
            <label class="form-label small mb-1">Session</label>
            <select name="session" class="form-control form-control-sm">
                @foreach($sessions as $sess)
                    <option value="{{ $sess }}" {{ $session == $sess ? 'selected' : '' }}>
                        {{ $sess === 'all' ? 'All Sessions / All Time' : (str_starts_with($sess, 'year_') ? 'Calendar Year ' . substr($sess, 5) : 'Session ' . $sess) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-3 col-md-4">
            <label class="form-label small mb-1">Team Member</label>
            <select name="sp_id" class="form-control form-control-sm">
                <option value="">All Members</option>
                @foreach($teamMembers as $sp)
                <option value="{{ $sp->id }}" {{ $spId == $sp->id ? 'selected' : '' }}>
                    {{ $sp->username }}{{ $sp->id == auth()->id() ? ' (Me)' : '' }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-3 col-md-4">
            <label class="form-label small mb-1">School</label>
            <select name="school_id" class="form-control form-control-sm">
                <option value="">All Schools</option>
                @foreach($schools as $school)
                <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>
                    [{{ $school->school_code }}] {{ $school->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-2 col-md-6">
            <label class="form-label small mb-1">Date From</label>
            <input type="date" name="date_from" class="form-control form-control-sm"
                   value="{{ $dateFrom ?? '' }}">
        </div>

        <div class="col-lg-2 col-md-6">
            <label class="form-label small mb-1">Date To</label>
            <input type="date" name="date_to" class="form-control form-control-sm"
                   value="{{ $dateTo ?? '' }}">
        </div>

        <div class="col-lg-12 d-flex gap-2 justify-content-end mt-2">
            <button class="btn btn-primary btn-sm">
                <i class="feather icon-filter me-1"></i> Filter
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="feather icon-x me-1"></i> Clear
            </a>
        </div>

    </form>
</div>


{{-- ═══ 7 WIDGETS ══════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- No. of Signed Schools --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="bg-white rounded shadow-sm p-3 text-center h-100">
            <div class="text-primary mb-1"><i class="feather icon-home fs-4"></i></div>
            <h4 class="fw-bold mb-0">{{ number_format($signedSchools) }}</h4>
            <small class="text-muted">Signed Schools</small>
        </div>
    </div>

    {{-- Total PO Amount --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="bg-white rounded shadow-sm p-3 text-center h-100">
            <div class="text-warning mb-1"><i class="feather icon-file-text fs-4"></i></div>
            <h5 class="fw-bold mb-0">₹{{ number_format($totalPoAmount, 0) }}</h5>
            <small class="text-muted"> Total PO Amount</small>
        </div>
    </div>

    {{-- Opening Outstanding --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="bg-white rounded shadow-sm p-3 text-center h-100 border border-warning">
            <div class="text-warning mb-1"><i class="feather icon-repeat fs-4"></i></div>
            <h5 class="fw-bold mb-0 text-warning">₹{{ number_format($totalOpeningOutstanding, 0) }}</h5>
            <small class="text-muted">Opening Outstanding</small>
        </div>
    </div>

    {{-- Total Billed --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="bg-white rounded shadow-sm p-3 text-center h-100">
            <div class="text-info mb-1"><i class="feather icon-layers fs-4"></i></div>
            <h5 class="fw-bold mb-0">₹{{ number_format($totalBillingAmount, 0) }}</h5>
            <small class="text-muted"> Total Billed</small>
        </div>
    </div>

    {{-- Pending PO --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="bg-white rounded shadow-sm p-3 text-center h-100">
            <div class="text-secondary mb-1"><i class="feather icon-clock fs-4"></i></div>
            <h5 class="fw-bold mb-0">₹{{ number_format($totalPendingPo, 0) }}</h5>
            <small class="text-muted"> Pending PO </small>
        </div>
    </div>

    {{-- Total Collection --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="bg-white rounded shadow-sm p-3 text-center h-100">
            <div class="text-success mb-1"><span class="fs-4">₹</span></div>
            <h5 class="fw-bold mb-0">₹{{ number_format($totalCollection, 0) }}</h5>
            <small class="text-muted">Total Collection</small>
        </div>
    </div>

    {{-- Outstanding --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="bg-white rounded shadow-sm p-3 text-center h-100
            {{ $totalOutstanding > 0 ? 'border border-danger' : '' }}">
            <div class="text-danger mb-1"><i class="feather icon-alert-circle fs-4"></i></div>
            <h5 class="fw-bold mb-0 {{ $totalOutstanding > 0 ? 'text-danger' : '' }}">
                ₹{{ number_format($totalOutstanding, 0) }}
            </h5>
            <small class="text-muted">Total Outstanding</small>
        </div>
    </div>

</div>


{{-- ═══ TARGET OVERVIEW ═════════════════════════════════════ --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="bg-white rounded shadow-sm p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">
                    <i class="feather icon-target text-primary me-2"></i>
                    Target vs Achievement — {{ $targetMonthLabel }}
                </h6>
                <a href="{{ route('targets.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="feather icon-list me-1"></i> View Targets
                </a>
            </div>
            <div class="row g-3 align-items-center">
                {{-- Left 50%: Target Amount & Achieved Amount --}}
                <div class="col-md-6">
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-3 rounded bg-light border">
                                <small class="text-muted d-block mb-1 fw-semibold">Target Amount</small>
                                <h5 class="fw-bold mb-0 text-dark">₹{{ number_format($targetAmount, 2) }}</h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded bg-light border">
                                <small class="text-muted d-block mb-1 fw-semibold">Achieved (Net Billed)</small>
                                <h5 class="fw-bold mb-0 text-success">₹{{ number_format($targetAchieved, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right 50%: Achieved Percentage & Progress Bar --}}
                <div class="col-md-6 ps-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-semibold text-muted">Achieved Percentage</span>
                        <span class="badge bg-{{ $targetPctColor }} fs-6">{{ $targetPct }}%</span>
                    </div>
                    <div class="progress" style="height: 24px; border-radius: 6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $targetPctColor }} fw-bold"
                             role="progressbar"
                             style="width: {{ min($targetPct, 100) }}%; font-size: 13px;"
                             aria-valuenow="{{ $targetPct }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            {{ $targetPct }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ═══ CHART ══════════════════════════════════════════════ --}}
<div class="row mb-4">
    <div class="col-xl-8">
        <div class="bg-white rounded shadow-sm p-3">
            <h6 class="fw-bold mb-3">PO Amount vs Collection — Session {{ $session }}</h6>
            <canvas id="sm-chart" height="90"></canvas>
        </div>
    </div>
    <div class="col-xl-4 mt-3 mt-xl-0">
        <div class="bg-white rounded shadow-sm p-3 h-100">
            <h6 class="fw-bold mb-3">Collection Ratio</h6>
            <canvas id="pie-chart" height="200"></canvas>
            <div class="text-center mt-2">
                @php $ratio = $totalBillingAmount > 0 ? round(($totalCollection / $totalBillingAmount) * 100) : 0; @endphp
                <span class="badge bg-{{ $ratio >= 70 ? 'success' : ($ratio >= 50 ? 'warning' : 'danger') }} fs-6">
                    {{ $ratio }}% Collected
                </span>
            </div>
        </div>
    </div>
</div>


{{-- ═══ APPROVAL QUEUE (Submitted POs awaiting SM action) ═══ --}}
@php
    $pendingApproval = \App\Models\Invoice::whereIn('user_id', array_keys($teamMembers->keyBy('id')->toArray()))
        ->where('status', \App\Models\Invoice::STATUS_SUBMITTED)
        ->with(['user:id,username','customer:id,name'])
        ->orderBy('invoice_date')
        ->get();
@endphp

@if($pendingApproval->count())
<div class="bg-white rounded shadow-sm p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
        <h6 class="fw-bold mb-0">
            <i class="feather icon-bell text-warning me-2"></i>
            Pending Approval Queue
            <span class="badge bg-warning text-dark ms-1">{{ $pendingApproval->count() }}</span>
        </h6>
    </div>
    <div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light">
            <tr>
                <th>PO Number</th>
                <th>Sales Person</th>
                <th>School</th>
                <th>Date</th>
                <th class="text-end">Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendingApproval as $po)
            <tr>
                <td><strong>{{ $po->po_number }}</strong></td>
                <td>{{ $po->user->username ?? '-' }}</td>
                <td>{{ $po->customer->name ?? '-' }}</td>
                <td>{{ $po->invoice_date->format('d M, Y') ?? '-' }}</td>
                <td class="text-end">₹{{ number_format($po->amount, 2) }}</td>
                <td>
                    <a href="{{ route('invoices.show', $po) }}" class="btn btn-xs btn-outline-primary btn-sm">
                        Review
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endif


{{-- ═══ SP × SCHOOL × FINANCIALS TABLE ═══════════════════════ --}}
<div class="bg-white rounded shadow-sm p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
        <h6 class="fw-bold mb-0">
            <i class="feather icon-list me-2 text-primary"></i>
            PO-wise Breakdown
        </h6>
        <a href="{{ route('reports.index') }}?{{ http_build_query(request()->all()) }}"
           class="btn btn-sm btn-outline-success">
            <i class="feather icon-download me-1"></i> Export Excel
        </a>
    </div>

    <div class="table-responsive">
    <table class="table table-bordered table-sm table-hover">
        <thead class="table-light">
            <tr>
                <th>PO Number</th>
                <th>SP Name</th>
                <th>School Name</th>
                <th class="text-end text-warning"> PO Amount</th>
                <th class="text-end text-info">Sales/Billing</th>
                <th class="text-end text-secondary">Pending PO</th>
                <th class="text-end text-success">Collection</th>
                <th class="text-end text-danger">Outstanding</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            {{-- 1. Opening Balance Rows from Past Sessions --}}
            @foreach($openingBalanceRows as $ob)
            <tr class="table-warning bg-opacity-25">
                <td>
                    <span class="badge bg-warning text-dark">
                        <i class="feather icon-repeat me-1"></i>Opening Balance ({{ $ob->session_year }})
                    </span>
                </td>
                <td>{{ $ob->salesPerson->username ?? '-' }}</td>
                <td>
                    <span title="{{ $ob->customer->school_code ?? '' }}">
                        {{ $ob->customer->name ?? '-' }}
                    </span>
                    <small class="text-muted d-block">{{ $ob->customer->school_code ?? '' }}</small>
                </td>
                <td class="text-end text-muted">₹0.00</td>
                <td class="text-end text-muted">₹0.00</td>
                <td class="text-end text-muted">₹0.00</td>
                <td class="text-end">₹{{ number_format($ob->opening_collected, 2) }}</td>
                <td class="text-end {{ $ob->opening_outstanding > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                    ₹{{ number_format($ob->opening_outstanding, 2) }}
                </td>
                <td>
                    <span class="badge bg-warning text-dark">Carry Forward</span>
                </td>
            </tr>
            @endforeach

            {{-- 2. Current Session PO Rows --}}
            @forelse($tableRows as $row)
            <tr>
                <td>
                    <a href="{{ route('invoices.show', $row->id) }}" class="text-primary">
                        {{ $row->po_number }}
                    </a>
                </td>
                <td>{{ $row->user->username ?? '-' }}</td>
                <td>
                    <span title="{{ $row->customer->school_code }}">
                        {{ $row->customer->name ?? '-' }}
                    </span>
                </td>
                <td class="text-end">₹{{ number_format($row->amount, 2) }}</td>
                <td class="text-end">₹{{ number_format($row->billing_amount, 2) }}</td>
                <td class="text-end">₹{{ number_format($row->amount - $row->billing_amount, 2) }}</td>
                <td class="text-end">₹{{ number_format($row->collected_amount, 2) }}</td>
                <td class="text-end {{ $row->outstanding_amount > 0 ? 'text-danger fw-bold' : '' }}">
                    ₹{{ number_format($row->outstanding_amount, 2) }}
                </td>
                <td>
                    @php
                        $badgeMap = ['approved'=>'success','submitted'=>'warning','draft'=>'secondary','rejected'=>'danger'];
                        $color = $badgeMap[$row->status] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $color }}">{{ ucfirst($row->status) }}</span>
                </td>
            </tr>
            @empty
            @if($openingBalanceRows->isEmpty())
            <tr>
                <td colspan="9" class="text-center text-muted py-4">No records found for selected filters.</td>
            </tr>
            @endif
            @endforelse
        </tbody>

        {{-- Totals Row --}}
        @if($tableRows->count() || $openingBalanceRows->count())
        <tfoot class="table-success fw-bold">
            <tr>
                <td colspan="3" class="text-end">Total</td>
                <td class="text-end">₹{{ number_format($tableRows->sum('amount'), 2) }}</td>
                <td class="text-end">₹{{ number_format($tableRows->sum('billing_amount'), 2) }}</td>
                <td class="text-end">₹{{ number_format($tableRows->sum(fn($r) => $r->amount - $r->billing_amount), 2) }}</td>
                <td class="text-end">₹{{ number_format($tableRows->sum('collected_amount') + $openingBalanceRows->sum('opening_collected'), 2) }}</td>
                <td class="text-end">₹{{ number_format($tableRows->sum('outstanding_amount') + $openingBalanceRows->sum('opening_outstanding'), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif

    </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // Selectize dropdowns
    $('select').selectize();

    const chartData = @json($chartData);

    // ── Bar chart: PO vs Collection ──────────────────────
    new Chart(document.getElementById('sm-chart'), {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'PO Amount ',
                    data: chartData.poData,
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1,
                },
                {
                    label: 'Collection ',
                    data: chartData.collectionData,
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1,
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => '₹' + v.toLocaleString() } }
            },
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // ── Pie chart: Collection ratio ───────────────────────
    const totalBilling    = {{ $totalBillingAmount }};
    const totalCollection = {{ $totalCollection }};
    const outstanding     = {{ $totalOutstanding }};

    new Chart(document.getElementById('pie-chart'), {
        type: 'doughnut',
        data: {
            labels: ['Collected ', 'Outstanding '],
            datasets: [{
                data: [totalCollection, Math.max(outstanding, 0)],
                backgroundColor: ['rgba(40,167,69,0.8)', 'rgba(220,53,69,0.8)'],
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });

});
</script>
@endpush
