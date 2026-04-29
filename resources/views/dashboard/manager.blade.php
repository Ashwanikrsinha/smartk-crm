@extends('layouts.dashboard')
@section('content')

{{-- ═══ HEADER ═══════════════════════════════════════════ --}}
<header class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0">Sales Manager Dashboard</h5>
        <small class="text-muted">Hello, {{ auth()->user()->username }} — Overview for {{ $year }}</small>
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

        <div class="col-lg-3 col-md-6">
            <label class="form-label small mb-1">Team Member</label>
            <select name="sp_id" class="form-control form-control-sm">
                <option value="">All Members</option>
                @foreach($teamMembers as $sp)
                <option value="{{ $sp->id }}" {{ $spId == $sp->id ? 'selected' : '' }}>
                    {{ $sp->username }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-3 col-md-6">
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

        <div class="col-lg-3 col-md-6">
            <label class="form-label small mb-1">Month / Year</label>
            <input type="month" name="month" class="form-control form-control-sm"
                   value="{{ $month ?? '' }}" placeholder="Filter by month">
        </div>

        <div class="col-lg-3 col-md-6">
            <label class="form-label small mb-1">Date From</label>
            <input type="date" name="date_from" class="form-control form-control-sm"
                   value="{{ $dateFrom ?? '' }}">
        </div>

        <div class="col-lg-3 col-md-6">
            <label class="form-label small mb-1">Date To</label>
            <input type="date" name="date_to" class="form-control form-control-sm"
                   value="{{ $dateTo ?? '' }}">
        </div>

        <div class="col-lg-3 col-md-6 d-flex gap-2">
            <button class="btn btn-primary btn-sm w-100">
                <i class="feather icon-filter me-1"></i> Filter
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="feather icon-x"></i>
            </a>
        </div>

    </form>
</div>


{{-- ═══ 6 WIDGETS ══════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- No. of Signed Schools --}}
    <div class="col-6 col-lg-2">
        <div class="bg-white rounded shadow-sm p-3 text-center h-100">
            <div class="text-primary mb-1"><i class="feather icon-home fs-4"></i></div>
            <h4 class="fw-bold mb-0">{{ number_format($signedSchools) }}</h4>
            <small class="text-muted">Signed Schools</small>
        </div>
    </div>

    {{-- A: Total PO Amount --}}
    <div class="col-6 col-lg-2">
        <div class="bg-white rounded shadow-sm p-3 text-center h-100">
            <div class="text-warning mb-1"><i class="feather icon-file-text fs-4"></i></div>
            <h5 class="fw-bold mb-0">₹{{ number_format($totalPoAmount, 0) }}</h5>
            <small class="text-muted"> Total PO Amount</small>
        </div>
    </div>

    {{-- B: Total Billed --}}
    <div class="col-6 col-lg-2">
        <div class="bg-white rounded shadow-sm p-3 text-center h-100">
            <div class="text-info mb-1"><i class="feather icon-layers fs-4"></i></div>
            <h5 class="fw-bold mb-0">₹{{ number_format($totalBillingAmount, 0) }}</h5>
            <small class="text-muted"> Total Billed</small>
        </div>
    </div>

    {{-- C: Pending PO --}}
    <div class="col-6 col-lg-2">
        <div class="bg-white rounded shadow-sm p-3 text-center h-100">
            <div class="text-secondary mb-1"><i class="feather icon-clock fs-4"></i></div>
            <h5 class="fw-bold mb-0">₹{{ number_format($totalPendingPo, 0) }}</h5>
            <small class="text-muted"> Pending PO </small>
        </div>
    </div>

    {{-- D: Total Collection --}}
    <div class="col-6 col-lg-2">
        <div class="bg-white rounded shadow-sm p-3 text-center h-100">
            <div class="text-success mb-1"><i class="feather icon-dollar-sign fs-4"></i></div>
            <h5 class="fw-bold mb-0">₹{{ number_format($totalCollection, 0) }}</h5>
            <small class="text-muted">Total Collection</small>
        </div>
    </div>

    {{-- E: Outstanding --}}
    <div class="col-6 col-lg-2">
        <div class="bg-white rounded shadow-sm p-3 text-center h-100
            {{ $totalOutstanding > 0 ? 'border border-danger' : '' }}">
            <div class="text-danger mb-1"><i class="feather icon-alert-circle fs-4"></i></div>
            <h5 class="fw-bold mb-0 {{ $totalOutstanding > 0 ? 'text-danger' : '' }}">
                ₹{{ number_format($totalOutstanding, 0) }}
            </h5>
            <small class="text-muted">Outstanding</small>
        </div>
    </div>

</div>


{{-- ═══ CHART ══════════════════════════════════════════════ --}}
<div class="row mb-4">
    <div class="col-xl-8">
        <div class="bg-white rounded shadow-sm p-3">
            <h6 class="fw-bold mb-3">PO Amount vs Collection — {{ $year }}</h6>
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
                <td>{{ $po->user->username }}</td>
                <td>{{ $po->customer->name }}</td>
                <td>{{ $po->invoice_date->format('d M, Y') }}</td>
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
            @forelse($tableRows as $row)
            <tr>
                <td>
                    <a href="{{ route('invoices.show', $row->id) }}" class="text-primary">
                        {{ $row->po_number }}
                    </a>
                </td>
                <td>{{ $row->user->username }}</td>
                <td>
                    <span title="{{ $row->customer->school_code }}">
                        {{ $row->customer->name }}
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
            <tr>
                <td colspan="9" class="text-center text-muted py-4">No records found for selected filters.</td>
            </tr>
            @endforelse
        </tbody>

        {{-- Totals Row --}}
        @if($tableRows->count())
        <tfoot class="table-success fw-bold">
            <tr>
                <td colspan="3" class="text-end">Total</td>
                <td class="text-end">₹{{ number_format($tableRows->sum('amount'), 2) }}</td>
                <td class="text-end">₹{{ number_format($tableRows->sum('billing_amount'), 2) }}</td>
                <td class="text-end">₹{{ number_format($tableRows->sum(fn($r) => $r->amount - $r->billing_amount), 2) }}</td>
                <td class="text-end">₹{{ number_format($tableRows->sum('collected_amount'), 2) }}</td>
                <td class="text-end">₹{{ number_format($tableRows->sum('outstanding_amount'), 2) }}</td>
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
