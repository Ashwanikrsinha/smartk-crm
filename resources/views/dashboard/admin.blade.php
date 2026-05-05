@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            @if (auth()->user()->isBusinessManager())
                <h5 class="mb-0">Business Manager —Dashboard</h5>
            @else
                <h5 class="mb-0">Admin — Marketing Dashboard</h5>
            @endif
            <small class="text-muted">System-wide overview</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary">
                <i class="feather icon-user-plus me-1"></i> Add User
            </a>
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
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
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


    {{-- ═══ 2 KEY WIDGETS ══════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-4 text-center">
                <div class="text-primary mb-2"><i class="feather icon-home fs-3"></i></div>
                <h3 class="fw-bold mb-0">{{ $signedSchools }}</h3>
                <p class="text-muted mb-0">Total Signed Schools</p>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-4 text-center">
                <div class="text-success mb-2"><i class="feather icon-trending-up fs-3"></i></div>
                <h3 class="fw-bold mb-0">₹{{ number_format($totalRevenue, 0) }}</h3>
                <p class="text-muted mb-0">Total Revenue</p>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-4 text-center">
                <div class="text-warning mb-2"><i class="feather icon-users fs-3"></i></div>
                <h3 class="fw-bold mb-0">{{ $totalSps }}</h3>
                <p class="text-muted mb-0">Active Sales Persons</p>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-4 text-center">
                <div class="text-danger mb-2"><i class="feather icon-alert-circle fs-3"></i></div>
                <h3 class="fw-bold mb-0">₹{{ number_format($totalOutstanding, 0) }}</h3>
                <p class="text-muted mb-0">Total Outstanding </p>
            </div>
        </div>

    </div>


    {{-- ═══ LEAD SOURCE BREAKDOWN ═══════════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="bg-white rounded shadow-sm p-3">
                <h6 class="fw-bold mb-3">Schools by Lead Source</h6>
                <canvas id="lead-chart" height="180"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="bg-white rounded shadow-sm p-3">
                <h6 class="fw-bold mb-3">Revenue Trend — {{ date('Y') }}</h6>
                <canvas id="revenue-chart" height="180"></canvas>
            </div>
        </div>
    </div>


    {{-- ═══ USER MANAGEMENT QUICK ACCESS ═══════════════════════ --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-6">
            <div class="bg-white rounded shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h6 class="fw-bold mb-0">
                        <i class="feather icon-users me-2 text-primary"></i> User Access
                    </h6>
                    <a href="{{ route('users.create') }}" class="btn btn-sm btn-outline-primary">
                        Create Account
                    </a>
                </div>
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Reports To</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentUsers as $u)
                            <tr>
                                <td>{{ $u->username }}</td>
                                <td><span class="badge bg-primary">{{ $u->role->name }}</span></td>
                                <td>{{ $u->reportiveTo?->username ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-link p-0">
                                        <i class="feather icon-edit-2"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary w-100 mt-1">
                    View All Users
                </a>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="bg-white rounded shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h6 class="fw-bold mb-0">
                        <i class="feather icon-database me-2 text-primary"></i> School Data
                    </h6>
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <p class="text-muted small mb-2">
                    All schools registered across all Sales Persons. All updated data is stored here.
                </p>
                <div class="row text-center g-2">
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <h5 class="fw-bold mb-0 text-primary">{{ $totalSchools }}</h5>
                            <small class="text-muted">Total Schools</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <h5 class="fw-bold mb-0 text-success">{{ $schoolsThisMonth }}</h5>
                            <small class="text-muted">This Month</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <h5 class="fw-bold mb-0 text-warning">{{ $totalPos }}</h5>
                            <small class="text-muted">Total POs</small>
                        </div>
                    </div>
                </div>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-success w-100 mt-3">
                    <i class="feather icon-download me-1"></i> Data Export
                </a>
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

            // Revenue trend line chart
            new Chart(document.getElementById('revenue-chart'), {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                            label: 'Revenue',
                            data: @json($chartData['poData']),
                            borderColor: 'rgba(40, 167, 69, 1)',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            fill: true,
                            tension: 0.3,
                        },
                        {
                            label: 'Collection',
                            data: @json($chartData['collectionData']),
                            borderColor: 'rgba(255, 193, 7, 1)',
                            backgroundColor: 'rgba(255, 193, 7, 0.1)',
                            fill: true,
                            tension: 0.3,
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

        });
    </script>
@endpush
