@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Marketing Dashboard</h5>
            <small class="text-muted">School & PO overview — read only</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-success">
                <i class="feather icon-download me-1"></i> Export
            </a>
        </div>
    </header>

    {{-- ═══ FILTERS ════════════════════════════════════════════ --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('dashboard') }}" class="row g-2 align-items-end">

            <div class="col-lg-3 col-md-6">
                <label class="form-label small mb-1">Lead From</label>
                <select name="lead_source_id" class="form-control form-control-sm">
                    <option value="">All Sources</option>
                    @foreach (\App\Models\LeadSource::orderBy('name')->get() as $src)
                        <option value="{{ $src->id }}" {{ request('lead_source_id') == $src->id ? 'selected' : '' }}>
                            {{ $src->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">Month</label>
                <input type="month" name="month" class="form-control form-control-sm" value="{{ request('month') }}">
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                    value="{{ request('date_from') }}">
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label small mb-1">State</label>
                <input type="text" name="state" class="form-control form-control-sm" value="{{ request('state') }}"
                    placeholder="e.g. Maharashtra">
            </div>

            <div class="col-lg-12 d-flex gap-2 justify-content-end mt-2">
                <button class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>

    {{-- ═══ SUMMARY WIDGETS (no collection / outstanding) ══════ --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-4 text-center">
                <div class="text-primary mb-2"><i class="feather icon-home fs-3"></i></div>
                <h3 class="fw-bold mb-0">{{ $totalSchools }}</h3>
                <p class="text-muted mb-0">Total Schools</p>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-4 text-center">
                <div class="text-success mb-2"><i class="feather icon-check-circle fs-3"></i></div>
                <h3 class="fw-bold mb-0">{{ $signedSchools }}</h3>
                <p class="text-muted mb-0">Signed Schools</p>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-4 text-center">
                <div class="text-warning mb-2"><i class="feather icon-file-text fs-3"></i></div>
                <h3 class="fw-bold mb-0">{{ $totalPos }}</h3>
                <p class="text-muted mb-0">Total POs</p>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-4 text-center">
                <div class="text-info mb-2"><i class="feather icon-trending-up fs-3"></i></div>
                <h3 class="fw-bold mb-0">₹{{ number_format($totalRevenue, 0) }}</h3>
                <p class="text-muted mb-0">Total PO Amount</p>
            </div>
        </div>

    </div>

    {{-- ═══ LEAD SOURCE BREAKDOWN ═══════════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="bg-white rounded shadow-sm p-3">
                <h6 class="fw-bold mb-3">Schools by Lead Source</h6>
                <canvas id="lead-chart" height="200"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="bg-white rounded shadow-sm p-3">
                <h6 class="fw-bold mb-3">PO Trend — {{ date('Y') }}</h6>
                <canvas id="po-chart" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- ═══ PO TABLE (no collected / outstanding columns) ══════ --}}
    <div class="bg-white rounded shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <h6 class="fw-bold mb-0">
                <i class="feather icon-list me-2 text-primary"></i> Purchase Orders
            </h6>
            <small class="text-muted">{{ $pos->count() }} records</small>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>PO Number</th>
                        <th>PO Date</th>
                        <th>School</th>
                        <th>School Code</th>
                        <th class="text-end">PO Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pos as $po)
                        <tr>
                            <td class="fw-bold">{{ $po->po_number }}</td>
                            <td>{{ $po->invoice_date->format('d M, Y') }}</td>
                            <td>{{ $po->customer->name ?? '—' }}</td>
                            <td><small class="text-muted">{{ $po->customer->school_code ?? '—' }}</small></td>
                            <td class="text-end">₹{{ number_format($po->amount, 2) }}</td>
                            <td>
                                @php
                                    $badgeClass = match ($po->status) {
                                        'approved' => 'bg-success',
                                        'submitted' => 'bg-warning text-dark',
                                        'rejected' => 'bg-danger',
                                        'draft' => 'bg-secondary',
                                        default => 'bg-light text-dark',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($po->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- School basics table --}}
        <div class="border-top pt-3 mt-3">
            <h6 class="fw-bold mb-3">
                <i class="feather icon-home me-2 text-primary"></i> Schools
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>School Name</th>
                            <th>School Code</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Phone</th>
                            <th>Lead Source</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($schools as $school)
                            <tr>
                                <td class="fw-bold">{{ $school->name }}</td>
                                <td><small class="text-muted">{{ $school->school_code ?? '—' }}</small></td>
                                <td>{{ $school->city ?? '—' }}</td>
                                <td>{{ $school->state ?? '—' }}</td>
                                <td>{{ $school->phone_number ?? '—' }}</td>
                                <td>{{ $school->leadSource?->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No schools found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $('select').selectize();

            // Lead Source pie chart
            new Chart(document.getElementById('lead-chart'), {
                type: 'pie',
                data: {
                    labels: @json($leadChartData['labels']),
                    datasets: [{
                        data: @json($leadChartData['data']),
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)',
                        ],
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

            // PO amount trend (no collection line — Marketing can't see it)
            new Chart(document.getElementById('po-chart'), {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'PO Amount',
                        data: @json($chartData['poData']),
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        fill: true,
                        tension: 0.3,
                    }]
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
        });
    </script>
@endpush
