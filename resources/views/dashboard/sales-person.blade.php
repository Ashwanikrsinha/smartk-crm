@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">My Dashboard</h5>
            <small class="text-muted">Hello, {{ auth()->user()->username }}</small>
        </div>
        <div class="d-flex gap-2">
            @can('create', App\Models\Invoice::class)
                <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-primary">
                    <i class="feather icon-plus me-1"></i> New PO
                </a>
            @endcan
        </div>
    </header>

    {{-- Month Filter --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('dashboard') }}" class="row g-2 align-items-end">
            <div class="col-lg-3">
                <label class="form-label small mb-1">Month</label>
                <input type="month" name="month" class="form-control form-control-sm" value="{{ $month ?? '' }}">
            </div>
            <div class="col-lg-2 d-flex gap-2">
                <button class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>


    {{-- ═══ 4 TOP WIDGETS ══════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-3 text-center">
                <div class="text-primary mb-1"><i class="feather icon-home fs-4"></i></div>
                <h4 class="fw-bold mb-0">{{ $noOfSchools }}</h4>
                <small class="text-muted">No. of Schools</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-3 text-center">
                <div class="text-warning mb-1"><i class="feather icon-trending-up fs-4"></i></div>
                <h5 class="fw-bold mb-0">₹{{ number_format($totalSaleAmount, 0) }}</h5>
                <small class="text-muted">Total Po Amount</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-3 text-center">
                <div class="text-warning mb-1"><i class="feather icon-trending-up fs-4"></i></div>
                <h5 class="fw-bold mb-0">₹{{ number_format($totalCollection+$totalBillingAmount, 0) }}</h5>
                <small class="text-muted">Total Sale Amount</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-3 text-center">
                <div class="text-success mb-1"><i class="feather icon-dollar-sign fs-4"></i></div>
                <h5 class="fw-bold mb-0">₹{{ number_format($totalCollection, 0) }}</h5>
                <small class="text-muted">Total Collection</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-3 text-center">
                <div class="text-success mb-1"><i class="feather icon-dollar-sign fs-4"></i></div>
                <h5 class="fw-bold mb-0">₹{{ number_format($totalBillingAmount, 0) }}</h5>
                <small class="text-muted">Total Billing Amount</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div
                class="bg-white rounded shadow-sm p-3 text-center
            {{ $totalPending > 0 ? 'border border-danger' : '' }}">
                <div class="text-danger mb-1"><i class="feather icon-alert-circle fs-4"></i></div>
                <h5 class="fw-bold mb-0 {{ $totalPending > 0 ? 'text-danger' : '' }}">
                    ₹{{ number_format($totalPending, 0) }}
                </h5>
                <small class="text-muted">Total Pending Amount</small>
            </div>
        </div>

    </div>


    {{-- ═══ CHART ══════════════════════════════════════════════ --}}
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="bg-white rounded shadow-sm p-3">
                <h6 class="fw-bold mb-3">My Monthly Performance — {{ $year }}</h6>
                <canvas id="sp-chart" height="100"></canvas>
            </div>
        </div>
        <div class="col-lg-4 mt-3 mt-lg-0">
            <div class="bg-white rounded shadow-sm p-3 h-100">
                <h6 class="fw-bold mb-3">Collection Ratio</h6>
                <canvas id="sp-pie" height="180"></canvas>
            </div>
        </div>
    </div>


    {{-- ═══ PENDING POs (My draft/submitted queue) ═════════════ --}}
    @if ($pendingPos->count())
        <div class="bg-white rounded shadow-sm p-3 mb-4">
            <h6 class="fw-bold border-bottom pb-2 mb-3">
                <i class="feather icon-clock text-warning me-2"></i>
                My Pending Orders
                <span class="badge bg-warning text-dark ms-1">{{ $pendingPos->count() }}</span>
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>PO Number</th>
                            <th>Date</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingPos as $po)
                            <tr>
                                <td><strong>{{ $po->po_number }}</strong></td>
                                <td>{{ $po->invoice_date->format('d M, Y') }}</td>
                                <td class="text-end">₹{{ number_format($po->amount, 2) }}</td>
                                <td>
                                    @php $c = ['draft'=>'secondary','submitted'=>'warning','rejected'=>'danger'][$po->status] ?? 'secondary'; @endphp
                                    <span class="badge bg-{{ $c }}">{{ ucfirst($po->status) }}</span>
                                </td>
                                <td>
                                    @if ($po->status === 'draft' || $po->status === 'rejected')
                                        <a href="{{ route('invoices.edit', $po) }}"
                                            class="btn btn-sm btn-outline-primary btn-sm">Edit</a>
                                    @else
                                        <a href="{{ route('invoices.show', $po) }}"
                                            class="btn btn-sm btn-outline-secondary btn-sm">View</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif


    {{-- ═══ SCHOOL-WISE FINANCIAL TABLE ═══════════════════════ --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <h6 class="fw-bold border-bottom pb-2 mb-3">
            <i class="feather icon-list me-2 text-primary"></i> School-wise Summary
        </h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th>School Name</th>
                        <th>PO No.</th>
                        <th class="text-end text-warning">Net Sale Amount</th>
                        <th class="text-end text-success">Collection</th>
                        <th class="text-end text-danger">Pending Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schoolRows as $row)
                        <tr>
                            <td>
                                <a href="{{ route('customers.show', $row->customer_id) }}" class="text-decoration-none">
                                    {{ $row->customer->name }}
                                    <small class="text-muted d-block">{{ $row->customer->school_code }}</small>
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('invoices.show', $row->id) }}" class="text-primary">
                                    {{ $row->po_number }}
                                </a>
                            </td>
                            <td class="text-end">₹{{ number_format($row->amount, 2) }}</td>
                            <td class="text-end">₹{{ number_format($row->collected_amount, 2) }}</td>
                            <td class="text-end {{ $row->outstanding_amount > 0 ? 'text-danger fw-bold' : '' }}">
                                ₹{{ number_format($row->outstanding_amount, 2) }}
                            </td>
                            <td>
                                @php $c = ['approved'=>'success','submitted'=>'warning','draft'=>'secondary','rejected'=>'danger'][$row->status] ?? 'secondary'; @endphp
                                <span class="badge bg-{{ $c }}">{{ ucfirst($row->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($schoolRows->count())
                    <tfoot class="table-success fw-bold">
                        <tr>
                            <td colspan="2" class="text-end">Total</td>
                            <td class="text-end">₹{{ number_format($schoolRows->sum('amount'), 2) }}</td>
                            <td class="text-end">₹{{ number_format($schoolRows->sum('collected_amount'), 2) }}</td>
                            <td class="text-end">₹{{ number_format($schoolRows->sum('outstanding_amount'), 2) }}</td>
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
        $(document).ready(function() {

            const chartData = @json($chartData);

            new Chart(document.getElementById('sp-chart'), {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                            label: 'Sale Amount',
                            data: chartData.poData,
                            backgroundColor: 'rgba(255,193,7,0.7)'
                        },
                        {
                            label: 'Collection',
                            data: chartData.collectionData,
                            backgroundColor: 'rgba(40,167,69,0.7)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: v => '₹' + v.toLocaleString()
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            new Chart(document.getElementById('sp-pie'), {
                type: 'doughnut',
                data: {
                    labels: ['Collected', 'Pending'],
                    datasets: [{
                        data: [{{ $totalCollection }}, {{ max($totalPending, 0) }}],
                        backgroundColor: ['rgba(40,167,69,0.8)', 'rgba(220,53,69,0.8)'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

        });
    </script>
@endpush
