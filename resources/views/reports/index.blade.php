@extends('layouts.dashboard')
@section('content')

    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Reports</h5>
            <small class="text-muted">Filter and export purchase order data</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.po-log-all', request()->all()) }}" class="btn btn-outline-info btn-sm">
                <i class="feather icon-activity me-1"></i> Export Update History (Logs)
            </a>
            <a href="{{ route('reports.export', request()->all()) }}" class="btn btn-success btn-sm">
                <i class="feather icon-download me-1"></i> Export Excel
            </a>
        </div>
    </header>


    {{-- ═══ FILTERS ════════════════════════════════════════════ --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('reports.index') }}" id="filter-form">

            <div class="row g-2 mb-2">

                {{-- SP Filter (hidden for SP role) --}}
                @if (!auth()->user()->isSalesPerson())
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small mb-1">Sales Person</label>
                        <select name="sp_id" class="form-control form-control-sm">
                            <option value="">All SPs</option>
                            @foreach ($teamMembers as $sp)
                                <option value="{{ $sp->id }}" {{ $spId == $sp->id ? 'selected' : '' }}>
                                    {{ $sp->username }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- School --}}
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small mb-1">School</label>
                    <select name="school_id" class="form-control form-control-sm">
                        <option value="">All Schools</option>
                        @foreach ($schools as $s)
                            <option value="{{ $s->id }}" {{ $schoolId == $s->id ? 'selected' : '' }}>
                                [{{ $s->school_code }}] {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Lead Source --}}
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small mb-1">Lead From</label>
                    <select name="lead_source_id" class="form-control form-control-sm">
                        <option value="">All Sources</option>
                        @foreach ($leadSources as $ls)
                            <option value="{{ $ls->id }}" {{ $leadSrcId == $ls->id ? 'selected' : '' }}>
                                {{ $ls->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- State --}}
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small mb-1">State</label>
                    <select name="state" class="form-control form-control-sm">
                        <option value="">All States</option>
                        @foreach ($states as $st)
                            <option value="{{ $st }}" {{ request('state') == $st ? 'selected' : '' }}>
                                {{ $st }}
                            </option>
                        @endforeach
                    </select>
                    {{-- <input type="text" name="state" class="form-control form-control-sm" list="state-list"
                        value="{{ $state ?? '' }}" placeholder="State...">
                    <datalist id="state-list">
                        @foreach ($states as $st)
                            <option value="{{ $st }}">
                        @endforeach
                    </datalist> --}}
                </div>

                {{-- Status --}}
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">All Statuses</option>
                        @foreach (['draft', 'submitted', 'approved', 'rejected'] as $s)
                            <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="row g-2 align-items-end">

                {{-- Month --}}
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small mb-1">Month</label>
                    <input type="month" name="month" class="form-control form-control-sm" value="{{ $month ?? '' }}"
                        id="month-filter">
                </div>

                {{-- Date Range --}}
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small mb-1">From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                        value="{{ $dateFrom ?? '' }}" id="date-from">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small mb-1">To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? '' }}"
                        id="date-to">
                </div>

                {{-- Year --}}
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small mb-1">Year</label>
                    <select name="year" class="form-control form-control-sm">
                        @for ($y = date('Y'); $y >= date('Y') - 4; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="feather icon-filter me-1"></i> Apply
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="feather icon-x"></i>
                    </a>
                </div>

            </div>

        </form>
    </div>


    {{-- ═══ SUMMARY WIDGETS ════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        @php
            $widgets = [
                ['label' => 'Total PO Amount', 'value' => $totals['po_amount'], 'color' => 'warning'],
                ['label' => 'Total Billed', 'value' => $totals['billing_amount'], 'color' => 'info'],
                ['label' => 'Pending PO', 'value' => $totals['pending_po'], 'color' => 'secondary'],
                ['label' => 'Total Collected', 'value' => $totals['collected'], 'color' => 'success'],
                ['label' => 'Outstanding', 'value' => $totals['outstanding'], 'color' => 'danger'],
            ];
        @endphp

        @foreach ($widgets as $w)
            <div class="col-6 col-lg">
                <div class="bg-white rounded shadow-sm p-3 text-center border-top border-{{ $w['color'] }} border-3">
                    <h5 class="fw-bold text-{{ $w['color'] }} mb-1">₹{{ number_format($w['value'], 0) }}</h5>
                    <small class="text-muted">{{ $w['label'] }}</small>
                </div>
            </div>
        @endforeach
    </div>


    {{-- ═══ RECORDS TABLE ══════════════════════════════════════ --}}
    <div class="bg-white rounded shadow-sm p-3">

        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <h6 class="fw-bold mb-0">
                <i class="feather icon-list me-2 text-primary"></i>
                Records
                <span class="badge bg-secondary ms-1">{{ $rows->count() }}</span>
            </h6>
            <a href="{{ route('reports.export', request()->all()) }}" class="btn btn-sm btn-outline-success">
                <i class="feather icon-download me-1"></i> Export Excel
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover" id="reports-table">
                <thead class="table-light">
                    <tr>
                        <th>PO Number</th>
                        <th>Date</th>
                        @if (!auth()->user()->isSalesPerson())
                            <th>SM Name</th>
                            <th>SP Name</th>
                        @endif
                        <th>School</th>
                        <th>State</th>
                        <th>Lead From</th>
                        <th class="text-end text-warning"> PO Amt</th>
                        <th class="text-end text-info">Billed</th>
                        <th class="text-end text-secondary">Pending PO</th>
                        <th class="text-end text-success">Total Collected</th>
                        <th class="text-end text-danger">Outstanding</th>
                        <th>Status</th>
                        <th>Delivery Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td>
                                <a href="{{ route('invoices.show', $row->id) }}" class="text-primary">
                                    {{ $row->po_number }}
                                </a>
                            </td>
                            <td>{{ $row->invoice_date->format('d M, Y') }}</td>
                            @if (!auth()->user()->isSalesPerson())
                                <td>{{ $row->user->reportiveTo?->username }}</td>
                                <td>{{ $row->user->username }}</td>
                            @endif
                            <td>
                                {{ $row->customer->name }}
                                <small class="text-muted d-block">{{ $row->customer->school_code }}</small>
                            </td>
                            <td>{{ $row->customer->state }}</td>
                            <td>{{ $row->customer->leadSource?->name ?? '—' }}</td>
                            <td class="text-end">₹{{ number_format($row->amount, 2) }}</td>
                            <td class="text-end">₹{{ number_format($row->billing_amount, 2) }}</td>
                            <td class="text-end">₹{{ number_format($row->amount - $row->billing_amount, 2) }}</td>
                            <td class="text-end">₹{{ number_format($row->collected_amount, 2) }}</td>
                            <td
                                class="text-end {{ $row->outstanding_amount > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                ₹{{ number_format($row->outstanding_amount, 2) }}
                            </td>
                            <td>
                                @php
                                    $map = [
                                        'approved' => 'success',
                                        'submitted' => 'warning',
                                        'draft' => 'secondary',
                                        'rejected' => 'danger',
                                    ];
                                    $c = $map[$row->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $c }}">{{ ucfirst($row->status) }}</span>
                            </td>
                            <td>{{ $row->delivery_due_date ? $row->delivery_due_date->format('d M, Y') : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center text-muted py-4">
                                No records found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if ($rows->count())
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="{{ auth()->user()->isSalesPerson() ? 5 : 6 }}">Total ({{ $rows->count() }}
                                records)</td>
                            <td class="text-end">₹{{ number_format($totals['po_amount'], 2) }}</td>
                            <td class="text-end">₹{{ number_format($totals['billing_amount'], 2) }}</td>
                            <td class="text-end">₹{{ number_format($totals['pending_po'], 2) }}</td>
                            <td class="text-end">₹{{ number_format($totals['collected'], 2) }}</td>
                            <td class="text-end">₹{{ number_format($totals['outstanding'], 2) }}</td>
                            <td colspan="2"></td>
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
            $('select').selectize();

            // If month is selected, clear date range and vice versa
            $('#month-filter').on('change', function() {
                if ($(this).val()) {
                    $('#date-from, #date-to').val('');
                }
            });
            $('#date-from, #date-to').on('change', function() {
                if ($(this).val()) {
                    $('#month-filter').val('');
                }
            });
        });
    </script>
@endpush
