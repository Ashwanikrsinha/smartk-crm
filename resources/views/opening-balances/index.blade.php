@extends('layouts.dashboard')
@section('content')

    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">School Opening Balances (Carry-Forward)</h5>
            <small class="text-muted">Manage historical & carry-forward balances across academic sessions</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('opening-balances.sample') }}" class="btn btn-outline-success btn-sm">
                <i class="feather icon-download me-1"></i> Sample Excel
            </a>
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#bulkUploadModal">
                <i class="feather icon-upload me-1"></i> Bulk Upload
            </button>
            <a href="{{ route('opening-balances.create') }}" class="btn btn-primary btn-sm">
                <i class="feather icon-plus me-1"></i> Add Opening Balance
            </a>
        </div>
    </header>

    {{-- ═══ ALERTS & IMPORT FEEDBACK ══════════════════════════ --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="feather icon-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('import_partial'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="feather icon-alert-triangle me-1"></i> {{ session('import_partial') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('import_errors'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading fw-bold mb-1"><i class="feather icon-alert-circle me-1"></i> Import Issues:</h6>
            <ul class="mb-0 ps-3 small">
                @foreach (session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ═══ FILTERS ════════════════════════════════════════════ --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('opening-balances.index') }}" class="row g-2 align-items-end">

            {{-- Session Filter --}}
            <div class="col-lg-3 col-md-4">
                <label class="form-label small mb-1 fw-bold">Academic Session</label>
                <select name="session_year" class="form-select form-select-sm" onchange="this.form.submit()">
                    @for ($y = date('Y') + 1; $y >= 2020; $y--)
                        @php
                            $label = "Session {$y}-" . str_pad(($y + 1) % 100, 2, '0', STR_PAD_LEFT);
                        @endphp
                        <option value="{{ $y }}" {{ $sessionYear == $y ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endfor
                </select>
            </div>

            {{-- School Filter --}}
            <div class="col-lg-4 col-md-4">
                <label class="form-label small mb-1">School</label>
                <select name="school_id" class="form-select form-select-sm selectize">
                    <option value="">All Schools</option>
                    @foreach ($schools as $sch)
                        <option value="{{ $sch->id }}" {{ $schoolId == $sch->id ? 'selected' : '' }}>
                            [{{ $sch->school_code }}] {{ $sch->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Sales Person Filter --}}
            <div class="col-lg-3 col-md-4">
                <label class="form-label small mb-1">Sales Person</label>
                <select name="sp_id" class="form-select form-select-sm selectize">
                    <option value="">All Sales Persons</option>
                    @foreach ($spUsers as $sp)
                        <option value="{{ $sp->id }}" {{ $spId == $sp->id ? 'selected' : '' }}>
                            {{ $sp->username }} ({{ $sp->emp_code ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="feather icon-filter me-1"></i> Apply
                </button>
                <a href="{{ route('opening-balances.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset">
                    <i class="feather icon-x"></i>
                </a>
            </div>

        </form>
    </div>

    {{-- ═══ FINANCIAL SUMMARY CARDS ═══════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="bg-white rounded shadow-sm p-3 text-center border-top border-warning border-3">
                <h5 class="fw-bold text-warning mb-1">
                    {{ $totals['opening_amount'] < 0 ? '-₹' . number_format(abs($totals['opening_amount']), 2) : '₹' . number_format($totals['opening_amount'], 2) }}
                </h5>
                <small class="text-muted">Total Opening Balance (Carry Forward)</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-white rounded shadow-sm p-3 text-center border-top border-success border-3">
                <h5 class="fw-bold text-success mb-1">₹{{ number_format($totals['opening_collected'], 2) }}</h5>
                <small class="text-muted">Total Collected</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-white rounded shadow-sm p-3 text-center border-top border-danger border-3">
                <h5 class="fw-bold text-danger mb-1">
                    {{ $totals['opening_outstanding'] < 0 ? '-₹' . number_format(abs($totals['opening_outstanding']), 2) : '₹' . number_format($totals['opening_outstanding'], 2) }}
                </h5>
                <small class="text-muted">Total Outstanding</small>
            </div>
        </div>
    </div>

    {{-- ═══ RECORDS TABLE ═════════════════════════════════════ --}}
    <div class="bg-white rounded shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <h6 class="fw-bold mb-0">
                <i class="feather icon-list me-2 text-primary"></i>
                Opening Balances
                <span class="badge bg-secondary ms-1">{{ $records->count() }}</span>
            </h6>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover align-middle" id="ob-table">
                <thead class="table-light">
                    <tr>
                        <th>School</th>
                        <th>Session</th>
                        <th>Sales Person</th>
                        <th class="text-end text-warning">Opening Amount</th>
                        <th class="text-end text-success">Collected</th>
                        <th class="text-end text-danger">Outstanding</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Source</th>
                        <th>Notes</th>
                        <th>Entered By</th>
                        <th class="text-center" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                        <tr>
                            <td>
                                <a href="{{ route('customers.show', $rec->customer_id) }}" class="fw-bold text-primary">
                                    {{ $rec->customer->name ?? '-' }}
                                </a>
                                <small class="text-muted d-block">{{ $rec->customer->school_code ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $rec->sessionLabel() }}
                                </span>
                            </td>
                            <td>
                                @if ($rec->salesPerson)
                                    {{ $rec->salesPerson->username }}
                                    <small class="text-muted d-block">({{ $rec->salesPerson->emp_code ?? '-' }})</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-warning">
                                {{ $rec->opening_amount < 0 ? '-₹' . number_format(abs($rec->opening_amount), 2) : '₹' . number_format($rec->opening_amount, 2) }}
                            </td>
                            <td class="text-end text-success">
                                ₹{{ number_format($rec->opening_collected, 2) }}
                            </td>
                            <td class="text-end fw-bold {{ $rec->opening_outstanding > 0 ? 'text-danger' : ($rec->opening_outstanding < 0 ? 'text-info' : 'text-success') }}">
                                {{ $rec->opening_outstanding < 0 ? '-₹' . number_format(abs($rec->opening_outstanding), 2) : '₹' . number_format($rec->opening_outstanding, 2) }}
                            </td>
                            <td class="text-center">
                                @if ($rec->status === 'return')
                                    <span class="badge bg-danger">
                                        <i class="feather icon-corner-up-left me-1"></i> Return
                                    </span>
                                @else
                                    <span class="badge bg-info text-dark">
                                        <i class="feather icon-repeat me-1"></i> Carry-Forward
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($rec->source === 'auto')
                                    <span class="badge bg-info text-dark">Auto</span>
                                @else
                                    <span class="badge bg-secondary">Manual</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $rec->notes ?? '—' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $rec->enteredBy->username ?? 'System' }}</small>
                                <small class="text-muted d-block" style="font-size:0.75rem;">
                                    {{ $rec->created_at ? $rec->created_at->format('d M, Y') : '' }}
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    {{-- Record Payment button if outstanding != 0 --}}
                                    @if ($rec->opening_outstanding != 0)
                                        <button type="button" class="btn btn-xs btn-outline-success collect-btn"
                                            data-bs-toggle="modal" data-bs-target="#collectModal"
                                            data-id="{{ $rec->id }}"
                                            data-school="{{ $rec->customer->name ?? '' }} ({{ $rec->customer->school_code ?? '' }})"
                                            data-outstanding="{{ $rec->opening_outstanding }}"
                                            title="Record Payment / Return">
                                            <i class="feather icon-check-circle"></i> Pay
                                        </button>
                                    @endif

                                    <a href="{{ route('opening-balances.edit', $rec) }}"
                                        class="btn btn-xs btn-outline-primary" title="Edit">
                                        <i class="feather icon-edit-2"></i>
                                    </a>

                                    <form action="{{ route('opening-balances.destroy', $rec) }}" method="POST"
                                        onsubmit="return confirm('Delete this opening balance record?');"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete">
                                            <i class="feather icon-trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                No opening balance records found for the selected session.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($records->count())
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="3">Total ({{ $records->count() }} records)</td>
                            <td class="text-end">₹{{ number_format($totals['opening_amount'], 2) }}</td>
                            <td class="text-end">₹{{ number_format($totals['opening_collected'], 2) }}</td>
                            <td class="text-end">₹{{ number_format($totals['opening_outstanding'], 2) }}</td>
                            <td colspan="5"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ═══ BULK UPLOAD MODAL ═════════════════════════════════ --}}
    <div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('opening-balances.bulk-upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkUploadModalLabel">
                            <i class="feather icon-upload me-1 text-primary"></i> Bulk Upload Opening Balances
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="feather icon-info me-1"></i>
                            Download the <strong>Sample Excel</strong> template to prepare your file. School codes must match existing schools in CRM.
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Target Academic Session <span class="text-danger">*</span></label>
                            <select name="session_year" class="form-select form-select-sm" required>
                                @for ($y = date('Y') + 1; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ $sessionYear == $y ? 'selected' : '' }}>
                                        Session {{ $y }}-{{ str_pad(($y + 1) % 100, 2, '0', STR_PAD_LEFT) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Select Excel File (.xlsx, .csv) <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control form-control-sm" accept=".xlsx,.xls,.csv" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="feather icon-upload me-1"></i> Upload & Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══ RECORD PAYMENT MODAL ═══════════════════════════════ --}}
    <div class="modal fade" id="collectModal" tabindex="-1" aria-labelledby="collectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="collectForm" method="POST" action="">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="collectModalLabel">
                            <i class="feather icon-credit-card me-1 text-success"></i> Record Payment Against Opening Balance
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label small text-muted mb-0">School</label>
                            <div class="fw-bold" id="modalSchoolName">—</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-0">Remaining Outstanding</label>
                            <div class="fw-bold text-danger fs-6" id="modalOutstanding">₹0.00</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold" id="modalAmountLabel">Payment Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="collected_amount" id="modalAmountInput"
                                class="form-control form-control-sm" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Payment Mode <span class="text-danger">*</span></label>
                                <select name="payment_mode" id="modalPaymentMode" class="form-select form-select-sm" required>
                                    <option value="cash">Cash</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="neft">NEFT / RTGS</option>
                                    <option value="upi">UPI</option>
                                    <option value="return">Return</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="collected_at" class="form-control form-control-sm"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        {{-- Dynamic Return Fields (shown only when Payment Mode is Return) --}}
                        <div id="obReturnFields" class="p-2 border rounded bg-light mb-3 d-none">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-danger">Billed Return Amount (₹)</label>
                                    <input type="number" step="0.01" name="billed_amount" id="modalBilledReturnInput"
                                        class="form-control form-control-sm" placeholder="0.00">
                                    <small class="text-muted" style="font-size:0.72rem;">Minus from Billed Amount</small>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-danger">GST on Return (₹)</label>
                                    <input type="number" step="0.01" name="gst_amount" id="modalGstReturnInput"
                                        class="form-control form-control-sm" placeholder="0.00" value="0.00">
                                    <small class="text-muted" style="font-size:0.72rem;">GST component</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Reference / Cheque / UTR / Return Slip No.</label>
                            <input type="text" name="reference_number" class="form-control form-control-sm"
                                placeholder="e.g. Cheque # / UTR # / Return Note #">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Remarks / Notes</label>
                            <input type="text" name="remarks" class="form-control form-control-sm"
                                placeholder="Optional notes">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="collectModalSubmitBtn" class="btn btn-success btn-sm">
                            <i class="feather icon-check me-1"></i> Save Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.selectize').selectize();

            // Toggle dynamic return fields
            $(document).on('change', '#modalPaymentMode', function() {
                if ($(this).val() === 'return') {
                    $('#obReturnFields').removeClass('d-none');
                    $('#modalAmountLabel').html('Return / Credit Amount (₹) <span class="text-danger">*</span>');
                    $('#collectModalSubmitBtn').removeClass('btn-success').addClass('btn-danger').html(
                        '<i class="feather icon-corner-up-left me-1"></i> Save Return Entry'
                    );
                } else {
                    $('#obReturnFields').addClass('d-none');
                    $('#modalAmountLabel').html('Payment Amount (₹) <span class="text-danger">*</span>');
                    $('#collectModalSubmitBtn').removeClass('btn-danger').addClass('btn-success').html(
                        '<i class="feather icon-check me-1"></i> Save Payment'
                    );
                }
            });

            // Setup collect modal dynamically
            $(document).on('click', '.collect-btn', function() {
                const id = $(this).data('id');
                const school = $(this).data('school');
                const outstanding = parseFloat($(this).data('outstanding')) || 0;

                $('#modalSchoolName').text(school);
                $('#modalOutstanding').text((outstanding < 0 ? '-₹' : '₹') + Math.abs(outstanding).toLocaleString('en-IN', { minimumFractionDigits: 2 }));
                $('#modalAmountInput').removeAttr('max').val(outstanding);
                $('#modalPaymentMode').val('cash').trigger('change');
                $('#modalBilledReturnInput').val('');
                $('#modalGstReturnInput').val('0.00');

                const url = '{{ route("opening-balances.collect", ":id") }}'.replace(':id', id);
                $('#collectForm').attr('action', url);
            });
        });
    </script>
@endpush
