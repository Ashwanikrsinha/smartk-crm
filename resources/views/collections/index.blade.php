@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Collections & Billing</h5>
            <small class="text-muted">Update billing  and collection  for approved orders</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bills.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="feather icon-file-text me-1"></i> CRM Bills
            </a>
            <a href="{{ route('reports.export', request()->all()) }}" class="btn btn-sm btn-outline-success">
                <i class="feather icon-download me-1"></i> Export
            </a>
        </div>
    </header>

    <div class="alert alert-info py-2 mb-4">
        <i class="feather icon-info me-2"></i>
        Enter <strong>Billed Amount </strong> for offline/Tally bills or
        <strong>Collection Amount </strong> for payments received.
        PDC cheques marked as "Cleared" are auto-included in D.
    </div>

    {{-- ═══ FILTERS ════════════════════════════════════════════ --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('collections.index') }}" class="row g-2 align-items-end">

            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">Team Member (SP)</label>
                <select name="sp_id" class="form-control form-control-sm">
                    <option value="">All SPs</option>
                    @foreach ($allSps as $sp)
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
        @php
            $widgets = [
                ['label' => 'Total PO Amount', 'val' => $totals['po_amount'], 'c' => 'warning'],
                ['label' => 'Total Billed', 'val' => $totals['billing_amount'], 'c' => 'info'],
                ['label' => 'Pending PO', 'val' => $totals['pending_po'], 'c' => 'secondary'],
                ['label' => 'Total Collected', 'val' => $totals['collected'], 'c' => 'success'],
                ['label' => 'Outstanding', 'val' => $totals['outstanding'], 'c' => 'danger'],
            ];
        @endphp
        @foreach ($widgets as $w)
            <div class="col-6 col-lg">
                <div class="bg-white rounded shadow-sm p-3 text-center border-top border-{{ $w['c'] }} border-3">
                    <h5 class="fw-bold text-{{ $w['c'] }} mb-1">₹{{ number_format($w['val'], 0) }}</h5>
                    <small class="text-muted">{{ $w['label'] }}</small>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ═══ MAIN TABLE ══════════════════════════════════════════ --}}
    <div class="bg-white rounded shadow-sm p-3">

        <h6 class="fw-bold border-bottom pb-2 mb-3">
            <i class="feather icon-list me-2 text-primary"></i>
            Order-wise Update — Enter Billed and Collection Amount
        </h6>

        <form action="{{ route('collections.bulk-store') }}" method="POST" id="collection-form">
            @csrf

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>PO Number</th>
                            <th>SP</th>
                            <th>School</th>
                            <th class="text-end text-warning" style="min-width:90px"> PO Amt</th>
                            <th class="text-end text-info" style="min-width:130px">
                                 Billed
                                <i class="feather icon-edit-2 text-info ms-1" title="Editable"></i>
                            </th>
                            <th class="text-end text-secondary" style="min-width:90px">Pend. PO</th>
                            <th class="text-end text-success" style="min-width:130px">
                                 Collected
                                <i class="feather icon-edit-2 text-success ms-1" title="Editable"></i>
                            </th>
                            <th class="text-end text-danger" style="min-width:90px">E: Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr id="row-{{ $row->id }}">
                                <td>
                                    <a href="{{ route('invoices.show', $row->id) }}" class="text-primary fw-bold">
                                        {{ $row->po_number }}
                                    </a>
                                    <input type="hidden" name="collections[{{ $row->id }}][invoice_id]"
                                        value="{{ $row->id }}">
                                </td>
                                <td class="small">{{ $row->user->username }}</td>
                                <td class="small">
                                    {{ $row->customer->name }}
                                    <span class="text-muted d-block"
                                        style="font-size:0.7rem">{{ $row->customer->school_code }}</span>
                                </td>

                                {{-- A: PO Amount --}}
                                <td class="text-end fw-bold text-warning">₹{{ number_format($row->amount, 0) }}</td>

                                {{-- B: Billed (editable — for manual/Tally billing) --}}
                                <td class="text-end">
                                    <div class="d-flex align-items-center gap-1 justify-content-end">
                                        <span class="text-muted small">₹</span>
                                        <input type="number" step="0.01" min="0"
                                            name="collections[{{ $row->id }}][billed_amount]"
                                            class="form-control form-control-sm text-end billed-input" style="width:100px"
                                            placeholder="0.00" data-invoice-id="{{ $row->id }}"
                                            data-po-amount="{{ $row->amount }}"
                                            data-collected="{{ $row->collected_amount }}">
                                    </div>
                                    <small class="text-muted d-block text-end" style="font-size:0.7rem">
                                        Current: ₹{{ number_format($row->billing_amount, 0) }}
                                    </small>
                                </td>

                                {{-- C: Pending PO (auto-calc) --}}
                                <td class="text-end text-secondary" id="pending-po-{{ $row->id }}">
                                    ₹{{ number_format($row->amount - $row->billing_amount, 0) }}
                                </td>

                                {{-- D: Collected (editable) --}}
                                <td class="text-end">
                                    <div class="d-flex align-items-center gap-1 justify-content-end">
                                        <span class="text-muted small">₹</span>
                                        <input type="number" step="0.01" min="0"
                                            name="collections[{{ $row->id }}][collected_amount]"
                                            class="form-control form-control-sm text-end collection-input"
                                            style="width:100px" placeholder="0.00" data-invoice-id="{{ $row->id }}"
                                            data-billing="{{ $row->billing_amount }}">
                                    </div>
                                    <small class="text-muted d-block text-end" style="font-size:0.7rem">
                                        Current: ₹{{ number_format($row->collected_amount, 0) }}
                                    </small>
                                </td>

                                {{-- E: Outstanding (auto-calc) --}}
                                <td class="text-end fw-bold {{ $row->outstanding_amount > 0 ? 'text-danger' : 'text-success' }}"
                                    id="outstanding-{{ $row->id }}">
                                    ₹{{ number_format($row->outstanding_amount, 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No approved orders found. Adjust filters and try again.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if ($rows->count())
                        <tfoot class="table-dark fw-bold">
                            <tr>
                                <td colspan="3" class="text-end">Total</td>
                                <td class="text-end">₹{{ number_format($totals['po_amount'], 0) }}</td>
                                <td class="text-end">₹{{ number_format($totals['billing_amount'], 0) }}</td>
                                <td class="text-end">₹{{ number_format($totals['pending_po'], 0) }}</td>
                                <td class="text-end">₹{{ number_format($totals['collected'], 0) }}</td>
                                <td class="text-end">₹{{ number_format($totals['outstanding'], 0) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            {{-- Payment / Billing Details Panel ─────────────────── --}}
            <div id="entry-details-panel" class="border rounded p-3 mt-3 bg-light d-none">
                <h6 class="fw-bold mb-3"><i class="feather icon-sliders me-2"></i>Entry Details</h6>
                <div class="row g-2">

                    <div class="col-lg-2">
                        <label class="form-label small">Payment Mode</label>
                        <select name="payment_mode" class="form-control form-control-sm">
                            <option value="cheque">Cheque</option>
                            <option value="neft">NEFT</option>
                            <option value="upi">UPI</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label small">Billing Source</label>
                        <select name="billing_source" class="form-control form-control-sm">
                            <option value="manual">Manual / Tally</option>
                            <option value="crm">CRM</option>
                        </select>
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label small">Bill Ref. No.</label>
                        <input type="text" name="billing_reference" class="form-control form-control-sm"
                            placeholder="Tally/Bill ref.">
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label small">Collection Ref. No.</label>
                        <input type="text" name="reference_number" class="form-control form-control-sm"
                            placeholder="Cheque/UTR/UPI ref.">
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label small">Date <span class="text-danger">*</span></label>
                        <input type="date" name="collected_at" class="form-control form-control-sm"
                            value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label small">Remarks</label>
                        <input type="text" name="collection_remarks" class="form-control form-control-sm"
                            placeholder="Optional notes...">
                    </div>

                </div>
            </div>

            @if ($rows->count())
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success"
                        onclick="return confirm('Save these billing and collection updates?')">
                        <i class="feather icon-save me-1"></i> Submit Updates
                    </button>
                </div>
            @endif

        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $('select').selectize();

            // Show entry details panel when any input is touched
            $(document).on('input', '.billed-input, .collection-input', function() {
                $('#entry-details-panel').removeClass('d-none');
                recalcRow($(this).closest('tr'));
            });

            function recalcRow(row) {
                const poAmt = parseFloat(row.find('.billed-input').data('po-amount')) || 0;
                const newBilled = parseFloat(row.find('.billed-input').val()) || 0;
                const newColl = parseFloat(row.find('.collection-input').val()) || 0;
                const invId = row.find('.billed-input').data('invoice-id');

                // Current stored values (already in DB)
                const curBilled = parseFloat(row.find('.billed-input').data('current-billed') || 0);
                const curColl = parseFloat(row.find('.collection-input').data('billing') || 0);

                // Preview: total after adding new entry
                const totalBilled = curBilled + newBilled;
                const totalColl = curColl + newColl;

                const pendingPo = Math.max(poAmt - totalBilled, 0);
                const outstanding = Math.max(totalBilled - totalColl, 0);

                $(`#pending-po-${invId}`).text('₹' + fmt(pendingPo));
                $(`#outstanding-${invId}`)
                    .text('₹' + fmt(outstanding))
                    .toggleClass('text-danger fw-bold', outstanding > 0)
                    .toggleClass('text-success', outstanding === 0);
            }

            function fmt(n) {
                return Math.round(n).toLocaleString('en-IN');
            }

        });
    </script>
@endpush
