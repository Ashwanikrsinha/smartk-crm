@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Collections & Billing</h5>
            <small class="text-muted">Update billing   and collection   per order</small>
        </div>
        <a href="{{ route('reports.export', request()->all()) }}" class="btn btn-sm btn-outline-success">
            <i class="feather icon-download me-1"></i> Export
        </a>
    </header>

    {{-- ═══ FILTERS ════════════════════════════════════════════ --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('collections.index') }}" class="row g-2 align-items-end">
            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">SP</label>
                <select name="sp_id" class="form-control form-control-sm">
                    <option value="">All SPs</option>
                    @foreach ($allSps as $sp)
                        <option value="{{ $sp->id }}" {{ $spId == $sp->id ? 'selected' : '' }}>{{ $sp->username }} ({{ $sp->emp_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label small mb-1">School</label>
                <select name="school_id" class="form-control form-control-sm">
                    <option value="">All Schools</option>
                    @foreach ($schools as $school)
                        <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>
                            [{{ $school->school_code }}] {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">PO Number</label>
                <input type="text" name="po_number" class="form-control form-control-sm" value="{{ $poNumber ?? '' }}"
                    placeholder="PO-2025-...">
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">Month</label>
                <input type="month" name="month" class="form-control form-control-sm" value="{{ $month ?? '' }}">
            </div>
            <div class="col-lg-3 d-flex gap-2">
                <button class="btn btn-primary btn-sm w-100">Filter</button>
                <a href="{{ route('collections.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>

    {{-- ═══ SUMMARY WIDGETS ════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        @foreach ([['PO Amount', $totals['po_amount'], 'warning'], ['Total Billed', $totals['billing_amount'], 'info'], [' Pending PO', $totals['pending_po'], 'secondary'], [' Collected', $totals['collected'], 'success'], [' Outstanding', $totals['outstanding'], 'danger']] as [$label, $val, $color])
            <div class="col-6 col-lg">
                <div class="bg-white rounded shadow-sm p-3 text-center border-top border-{{ $color }} border-3">
                    <h5 class="fw-bold text-{{ $color }} mb-1">₹{{ number_format($val, 0) }}</h5>
                    <small class="text-muted">{{ $label }}</small>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ═══ PO TABLE WITH PER-ROW ENTRY PANELS ════════════════ --}}
    <div class="bg-white rounded shadow-sm p-3">
        <h6 class="fw-bold border-bottom pb-2 mb-0">
            <i class="feather icon-list me-2 text-primary"></i>
            Order-wise — Click a row to update Billing   or Collection
        </h6>

        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>PO Number</th>
                        <th>SP</th>
                        <th>School</th>
                        <th class="text-end text-warning">PO Amt</th>
                        <th class="text-end text-info">
                            Billed <i class="feather icon-edit-2 ms-1" style="font-size:0.7rem"></i>
                        </th>
                        <th class="text-end text-secondary"> Pend. PO</th>
                        <th class="text-end text-success">
                             Collected <i class="feather icon-edit-2 ms-1" style="font-size:0.7rem"></i>
                        </th>
                        <th class="text-end text-danger"> Outstanding</th>
                        <th class="text-center">Entry</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        {{-- ── Data row ────────────────────────────── --}}
                        <tr class="po-row" data-id="{{ $row->id }}">
                            <td>
                                <a href="{{ route('invoices.show', $row->id) }}" class="text-primary fw-bold">
                                    {{ $row->po_number }}
                                </a>
                            </td>
                            <td class="small">{{ $row->user->username }}</td>
                            <td class="small">
                                {{ $row->customer->name }}
                                <span class="text-muted d-block"
                                    style="font-size:0.7rem">{{ $row->customer->school_code }}</span>
                            </td>
                            <td class="text-end fw-bold text-warning">₹{{ number_format($row->amount, 0) }}</td>
                            <td class="text-end" id="billed-cell-{{ $row->id }}">
                                ₹{{ number_format($row->billing_amount, 0) }}</td>
                            <td class="text-end" id="pending-po-cell-{{ $row->id }}">
                                ₹{{ number_format($row->pending_po_amount, 0) }}</td>
                            <td class="text-end" id="collected-cell-{{ $row->id }}">
                                ₹{{ number_format($row->collected_amount, 0) }}</td>
                            <td class="text-end fw-bold {{ $row->outstanding_amount > 0 ? 'text-danger' : 'text-success' }}"
                                id="outstanding-cell-{{ $row->id }}">
                                ₹{{ number_format($row->outstanding_amount, 0) }}
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary toggle-entry-panel"
                                    data-id="{{ $row->id }}" title="Add entry">
                                    <i class="feather icon-plus-circle"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- ── Per-row entry panel (hidden by default) ─ --}}
                        <tr class="entry-panel-row d-none" id="entry-panel-{{ $row->id }}">
                            <td colspan="9" class="p-0">
                                <div class="border-start border-4 border-primary bg-light p-3">

                                    <div class="row g-2 mb-2">
                                        <div class="col-12">
                                            <small class="text-muted fw-bold text-uppercase">
                                                Entry for {{ $row->po_number }} — {{ $row->customer->name }}
                                                <span class="text-info ms-2">
                                                    Current Billed: ₹{{ number_format($row->billing_amount, 2) }}
                                                </span>
                                                <span class="text-success ms-2">
                                                    Current Collected: ₹{{ number_format($row->collected_amount, 2) }}
                                                </span>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="row g-2">
                                        {{-- Billed amount input --}}
                                        <div class="col-lg-2">
                                            <label class="form-label small mb-1 text-info fw-bold">
                                                Add Billing Amount (₹)
                                            </label>
                                            <input type="number" step="0.01" min="0"
                                                class="form-control form-control-sm billed-input"
                                                data-id="{{ $row->id }}" placeholder="0.00">
                                        </div>

                                        {{--  Collection amount input --}}
                                        <div class="col-lg-2">
                                            <label class="form-label small mb-1 text-success fw-bold">
                                                 Add Collection Amount (₹)
                                            </label>
                                            <input type="number" step="0.01" min="0"
                                                class="form-control form-control-sm collected-input"
                                                data-id="{{ $row->id }}" placeholder="0.00">
                                        </div>

                                        {{-- Payment Mode --}}
                                        <div class="col-lg-2">
                                            <label class="form-label small mb-1">Payment Mode</label>
                                            <select class="form-control form-control-sm payment-mode"
                                                data-id="{{ $row->id }}">
                                                <option value="cheque">Cheque</option>
                                                <option value="neft">NEFT</option>
                                                <option value="upi">UPI</option>
                                                <option value="cash">Cash</option>
                                            </select>
                                        </div>

                                        {{-- Billing Source --}}
                                        <div class="col-lg-2">
                                            <label class="form-label small mb-1">Bill Source</label>
                                            <select class="form-control form-control-sm billing-source"
                                                data-id="{{ $row->id }}">
                                                <option value="manual">Manual/Tally</option>
                                                <option value="crm">CRM</option>
                                            </select>
                                        </div>

                                        {{-- Bill Ref --}}
                                        <div class="col-lg-2">
                                            <label class="form-label small mb-1">Bill Ref. No.</label>
                                            <input type="text" class="form-control form-control-sm billing-reference"
                                                data-id="{{ $row->id }}" placeholder="Tally/bill ref.">
                                        </div>

                                        {{-- Collection Ref --}}
                                        <div class="col-lg-2">
                                            <label class="form-label small mb-1">Collection Ref. No.</label>
                                            <input type="text"
                                                class="form-control form-control-sm collection-reference"
                                                data-id="{{ $row->id }}" placeholder="Cheque/UTR/UPI ref.">
                                        </div>

                                        {{-- Date --}}
                                        <div class="col-lg-2">
                                            <label class="form-label small mb-1">Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control form-control-sm entry-date"
                                                data-id="{{ $row->id }}" value="{{ date('Y-m-d') }}">
                                        </div>

                                        {{-- Remarks --}}
                                        <div class="col-lg-2">
                                            <label class="form-label small mb-1">Remarks</label>
                                            <input type="text" class="form-control form-control-sm entry-remarks"
                                                data-id="{{ $row->id }}" placeholder="Optional notes...">
                                        </div>

                                        {{-- Save button (full width on next row) --}}
                                        <div class="col-12 d-flex gap-2 align-items-center mt-1">
                                            <button class="btn btn-success btn-sm save-entry-btn"
                                                data-id="{{ $row->id }}">
                                                <i class="feather icon-save me-1"></i> Save Entry
                                            </button>
                                            <button class="btn btn-outline-secondary btn-sm cancel-entry-btn"
                                                data-id="{{ $row->id }}">
                                                Cancel
                                            </button>
                                            <span
                                                class="entry-feedback-{{ $row->id }} text-success small ms-2 d-none">
                                                ✓ Saved successfully
                                            </span>
                                            <span
                                                class="entry-error-{{ $row->id }} text-danger small ms-2 d-none"></span>
                                        </div>
                                    </div>

                                    {{-- Recent Logs for this PO --}}
                                    <div class="mt-3 border-top pt-2">
                                        <h6 class="small fw-bold text-muted mb-2">Recent Activity Logs (Ledger)</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered bg-white mb-0" style="font-size:0.75rem">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Action</th>
                                                        <th class="text-end">Amount</th>
                                                        <th class="text-end">PO Amt</th>
                                                        <th class="text-end text-info">Billed</th>
                                                        <th class="text-end text-success">Collected</th>
                                                        <th class="text-end text-danger">Outstanding</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="logs-body-{{ $row->id }}">
                                                    <tr>
                                                        <td colspan="7" class="text-center py-2 text-muted">Loading logs...</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No approved orders found.</td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- Totals row --}}
                @if ($rows->count())
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">Total</td>
                            <td class="text-end">₹{{ number_format($totals['po_amount'], 0) }}</td>
                            <td class="text-end">₹{{ number_format($totals['billing_amount'], 0) }}</td>
                            <td class="text-end">₹{{ number_format($totals['pending_po'], 0) }}</td>
                            <td class="text-end">₹{{ number_format($totals['collected'], 0) }}</td>
                            <td class="text-end">₹{{ number_format($totals['outstanding'], 0) }}</td>
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

            $('select').selectize();

            // ── Toggle per-row entry panel ────────────────────────
            $(document).on('click', '.toggle-entry-panel', function() {
                var id = $(this).data('id');
                var $panel = $('#entry-panel-' + id);
                $panel.toggleClass('d-none');
                $(this).find('i').toggleClass('icon-plus-circle icon-minus-circle');

                if (!$panel.hasClass('d-none')) {
                    fetchLogs(id);
                }
            });

            function fetchLogs(id) {
                var $body = $('#logs-body-' + id);
                $body.html('<tr><td colspan="7" class="text-center py-2 text-muted">Loading logs...</td></tr>');

                $.get('/collections/invoice/' + id, function(res) {
                    if (!res.logs || res.logs.length === 0) {
                        $body.html('<tr><td colspan="7" class="text-center py-2 text-muted">No logs found.</td></tr>');
                        return;
                    }

                    var html = '';
                    res.logs.forEach(function(log) {
                        var date = new Date(log.created_at).toLocaleDateString('en-IN', {
                            day: '2-digit', month: 'short', year: 'numeric'
                        });
                        var actionLabel = log.action.charAt(0).toUpperCase() + log.action.slice(1);
                        var amount = log.amount > 0 ? '₹' + formatNum(log.amount) : '—';

                        html += '<tr>' +
                            '<td>' + date + '</td>' +
                            '<td>' + actionLabel + '</td>' +
                            '<td class="text-end fw-bold">' + amount + '</td>' +
                            '<td class="text-end">₹' + formatNum(log.snapshot_po_amount) + '</td>' +
                            '<td class="text-end text-info">₹' + formatNum(log.snapshot_billed_amount) + '</td>' +
                            '<td class="text-end text-success">₹' + formatNum(log.snapshot_collected) + '</td>' +
                            '<td class="text-end text-danger fw-bold">₹' + formatNum(log.snapshot_outstanding) + '</td>' +
                        '</tr>';
                    });
                    $body.html(html);
                });
            }

            $(document).on('click', '.cancel-entry-btn', function() {
                var id = $(this).data('id');
                $('#entry-panel-' + id).addClass('d-none');
                $('.toggle-entry-panel[data-id="' + id + '"] i')
                    .removeClass('icon-minus-circle').addClass('icon-plus-circle');
                resetPanel(id);
            });

            // ── Save entry (AJAX) ─────────────────────────────────
            $(document).on('click', '.save-entry-btn', function() {
                var id = $(this).data('id');
                var $btn = $(this);
                var $ok = $('.entry-feedback-' + id);
                var $err = $('.entry-error-' + id);

                var billedAmt = parseFloat($('.billed-input[data-id="' + id + '"]').val()) || 0;
                var collectedAmt = parseFloat($('.collected-input[data-id="' + id + '"]').val()) || 0;
                var entryDate = $('.entry-date[data-id="' + id + '"]').val();

                if (billedAmt <= 0 && collectedAmt <= 0) {
                    $err.text('Enter at least one amount (B or D).').removeClass('d-none');
                    $ok.addClass('d-none');
                    return;
                }
                if (!entryDate) {
                    $err.text('Date is required.').removeClass('d-none');
                    return;
                }

                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Saving...');
                $ok.addClass('d-none');
                $err.addClass('d-none');

                $.ajax({
                    url: '{{ route('collections.save-single') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        invoice_id: id,
                        billed_amount: billedAmt,
                        collected_amount: collectedAmt,
                        payment_mode: $('.payment-mode[data-id="' + id + '"]').val(),
                        billing_source: $('.billing-source[data-id="' + id + '"]').val(),
                        billing_reference: $('.billing-reference[data-id="' + id + '"]').val(),
                        reference_number: $('.collection-reference[data-id="' + id + '"]').val(),
                        entry_date: entryDate,
                        remarks: $('.entry-remarks[data-id="' + id + '"]').val(),
                    },
                    success: function(res) {
                        // Update cells live
                        $('#billed-cell-' + id).text('₹' + formatNum(res.billing_amount));
                        $('#pending-po-cell-' + id).text('₹' + formatNum(res.pending_po));
                        $('#collected-cell-' + id).text('₹' + formatNum(res.collected_amount));
                        $('#outstanding-cell-' + id)
                            .text('₹' + formatNum(res.outstanding))
                            .toggleClass('text-danger fw-bold', res.outstanding > 0)
                            .toggleClass('text-success', res.outstanding <= 0);

                        $ok.removeClass('d-none');
                        fetchLogs(id); // Refresh logs
                        resetPanel(id);
                        $btn.prop('disabled', false).html(
                            '<i class="feather icon-save me-1"></i> Save Entry');
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON?.error || 'Save failed. Please try again.';
                        $err.text(msg).removeClass('d-none');
                        $btn.prop('disabled', false).html(
                            '<i class="feather icon-save me-1"></i> Save Entry');
                    }
                });
            });

            function resetPanel(id) {
                $('.billed-input[data-id="' + id + '"]').val('');
                $('.collected-input[data-id="' + id + '"]').val('');
                $('.billing-reference[data-id="' + id + '"]').val('');
                $('.collection-reference[data-id="' + id + '"]').val('');
                $('.entry-remarks[data-id="' + id + '"]').val('');
                $('.entry-date[data-id="' + id + '"]').val('{{ date('Y-m-d') }}');
            }

            function formatNum(n) {
                return (parseFloat(n) || 0).toLocaleString('en-IN', {
                    minimumFractionDigits: 0
                });
            }
        });
    </script>
@endpush
