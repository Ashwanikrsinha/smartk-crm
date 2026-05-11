@extends('layouts.dashboard')
@section('content')

    @php
        $isMarketing = auth()->user()->isMarketing();
        $isSp = auth()->user()->isSalesPerson();
    @endphp

    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Reports</h5>
            <small class="text-muted">Filter and export purchase order data</small>
        </div>
        {{-- Marketing: no exports at all --}}
        <div class="d-flex gap-2">
            @unless ($isMarketing)
                <a href="{{ route('reports.po-log-all', request()->all()) }}" class="btn btn-outline-info btn-sm">
                    <i class="feather icon-activity me-1"></i> Export Update History (Logs)
                </a>
            @endunless
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
                @if (!$isSp)
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small mb-1">Sales Person</label>
                        <select name="sp_id" class="form-control form-control-sm">
                            <option value="">All SPs</option>
                            @foreach ($teamMembers as $sp)
                                <option value="{{ $sp->id }}" {{ $spId == $sp->id ? 'selected' : '' }}>
                                    {{ $sp->username }} ({{ $sp->emp_code }})
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

                <div class="col-lg-3 col-md-6">
                    <label class="form-label small mb-1">Month</label>
                    <input type="month" name="month" class="form-control form-control-sm" value="{{ $month ?? '' }}"
                        id="month-filter">
                </div>

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


    {{-- ═══ SUMMARY WIDGETS ════════════════════════════════════
         Marketing: PO Amount, Billed, Pending PO only
         Others:    all five
    ════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        @php
            $widgets = [
                ['label' => 'Total PO Amount', 'value' => $totals['po_amount'], 'color' => 'warning', 'show' => true],
                ['label' => 'Total Billed', 'value' => $totals['billing_amount'], 'color' => 'info', 'show' => !$isMarketing,],
                ['label' => 'Pending PO', 'value' => $totals['pending_po'], 'color' => 'secondary', 'show' => !$isMarketing,],
                [
                    'label' => 'Total Collected',
                    'value' => $totals['collected'],
                    'color' => 'success',
                    'show' => !$isMarketing,
                ],
                [
                    'label' => 'Outstanding',
                    'value' => $totals['outstanding'],
                    'color' => 'danger',
                    'show' => !$isMarketing,
                ],
            ];
        @endphp

        @foreach ($widgets as $w)
            @if ($w['show'])
                <div class="col-6 col-lg">
                    <div class="bg-white rounded shadow-sm p-3 text-center border-top border-{{ $w['color'] }} border-3">
                        <h5 class="fw-bold text-{{ $w['color'] }} mb-1">
                            ₹{{ number_format($w['value'], 0) }}
                        </h5>
                        <small class="text-muted">{{ $w['label'] }}</small>
                    </div>
                </div>
            @endif
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
            {{-- Marketing: no export button here either --}}
            @unless ($isMarketing)
                <a href="{{ route('reports.export', request()->all()) }}" class="btn btn-sm btn-outline-success">
                    <i class="feather icon-download me-1"></i> Export Excel
                </a>
            @endunless
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover" id="reports-table">
                <thead class="table-light">
                    <tr>
                        <th>PO Number</th>
                        <th>Date</th>
                        @if (!$isSp)
                            <th>SM Name</th>
                            <th>SP Name</th>
                        @endif
                        <th>School</th>
                        <th>State</th>
                        <th>Lead From</th>
                        <th class="text-end text-warning">PO Amt</th>
                        @unless ($isMarketing)
                        <th class="text-end text-info">Billed</th>
                        <th class="text-end text-secondary">Pending PO</th>
                        <th class="text-end text-success">Total Collected</th>
                        <th class="text-end text-danger">Outstanding</th>
                        @endunless
                        <th>Status</th>
                        @unless ($isMarketing)
                        <th>Delivery Date</th>
                        @endunless
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td>
                                {{-- Marketing: plain text, no clickable link to PO detail --}}
                                @if ($isMarketing)
                                    {{ $row->po_number }}
                                @else
                                    <a href="{{ route('invoices.show', $row->id) }}" class="text-primary">
                                        {{ $row->po_number }}
                                    </a>
                                @endif
                            </td>
                            <td>{{ $row->invoice_date->format('d M, Y') }}</td>
                            @if (!$isSp)
                                <td>{{ $row->user->reportiveTo?->username }} ({{ $row->user->reportiveTo?->emp_code }})
                                </td>
                                <td>{{ $row->user->username }} ({{ $row->user->emp_code }})</td>
                            @endif
                            <td>
                                {{ $row->customer->name }}
                                <small class="text-muted d-block">{{ $row->customer->school_code }}</small>
                            </td>
                            <td>{{ $row->customer->state }}</td>
                            <td>{{ $row->customer->leadSource?->name ?? '—' }}</td>
                            <td class="text-end">₹{{ number_format($row->amount, 2) }}</td>
                            @unless ($isMarketing)
                                <td class="text-end">₹{{ number_format($row->billing_amount, 2) }}</td>
                                <td class="text-end">₹{{ number_format($row->amount - $row->billing_amount, 2) }}</td>
                                <td class="text-end">₹{{ number_format($row->collected_amount, 2) }}</td>
                                <td
                                    class="text-end {{ $row->outstanding_amount > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                    ₹{{ number_format($row->outstanding_amount, 2) }}
                                </td>
                            @endunless
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
                            @unless ($isMarketing)
                                <td>{{ $row->delivery_due_date ? $row->delivery_due_date->format('d M, Y') : '—' }}</td>
                            @endunless
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center text-muted py-4">
                                No records found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if ($rows->count())
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            {{--
                                colspan shifts based on hidden columns:
                                SP role hides SM+SP cols (2 less), Marketing hides collected+outstanding.
                                Base non-financial cols before amounts: PO#, Date, [SM], [SP], School, State, Lead = 5 or 7
                            --}}
                            @php
                                $baseColspan = $isSp ? 5 : 7; // PO#, Date, (SM, SP), School, State, Lead
                            @endphp
                            <td colspan="{{ $baseColspan }}">
                                Total ({{ $rows->count() }} records)
                            </td>
                            <td class="text-end">₹{{ number_format($totals['po_amount'], 2) }}</td>
                            @unless ($isMarketing)
                            <td class="text-end">₹{{ number_format($totals['billing_amount'], 2) }}</td>
                            <td class="text-end">₹{{ number_format($totals['pending_po'], 2) }}</td>
                                <td class="text-end">₹{{ number_format($totals['collected'], 2) }}</td>
                                <td class="text-end">₹{{ number_format($totals['outstanding'], 2) }}</td>
                            @endunless
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

            $('#month-filter').on('change', function() {
                if ($(this).val()) $('#date-from, #date-to').val('');
            });
            $('#date-from, #date-to').on('change', function() {
                if ($(this).val()) $('#month-filter').val('');
            });
        });
    </script>
@endpush
