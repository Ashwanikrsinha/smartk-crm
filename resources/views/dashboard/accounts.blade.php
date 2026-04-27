@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Accounts Dashboard</h5>
            <small class="text-muted">Update collection details for approved orders</small>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-success">
            <i class="feather icon-download me-1"></i> Export Excel
        </a>
    </header>

    {{-- Info banner --}}
    <div class="alert alert-info py-2 mb-4">
        <i class="feather icon-info me-2"></i>
        Search by school name, SP name, or PO number. Enter collected amount and click Submit to update.
        The pending amount will be automatically reflected in SP and SM dashboards.
    </div>

    {{-- ═══ FILTERS ════════════════════════════════════════════ --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('dashboard') }}" class="row g-2 align-items-end">

            <div class="col-lg-3 col-md-6">
                <label class="form-label small mb-1">Team Member (SP)</label>
                <select name="sp_id" class="form-control form-control-sm">
                    <option value="">All Members</option>
                    @foreach ($allSps as $sp)
                        <option value="{{ $sp->id }}" {{ $spId == $sp->id ? 'selected' : '' }}>
                            {{ $sp->username }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label small mb-1">School Name</label>
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
                <label class="form-label small mb-1">PO / School ID</label>
                <input type="text" name="po_number" class="form-control form-control-sm" placeholder="PO-2025-0001..."
                    value="{{ $poNumber ?? '' }}">
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">Month</label>
                <input type="month" name="month" class="form-control form-control-sm" value="{{ $month ?? '' }}">
            </div>

            <div class="col-lg-2 d-flex gap-2">
                <button class="btn btn-primary btn-sm w-100">Filter</button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>

        </form>
    </div>


    {{-- ═══ TOTALS ROW ═════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-3 text-center">
                <h6 class="text-muted small mb-1">Total PO Amount </h6>
                <h5 class="fw-bold text-warning mb-0">₹{{ number_format($totalPo, 2) }}</h5>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-3 text-center">
                <h6 class="text-muted small mb-1">Total Billed </h6>
                <h5 class="fw-bold text-info mb-0">₹{{ number_format($totalBilling, 2) }}</h5>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-3 text-center">
                <h6 class="text-muted small mb-1">Total Collected </h6>
                <h5 class="fw-bold text-success mb-0">₹{{ number_format($totalCollected, 2) }}</h5>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div
                class="bg-white rounded shadow-sm p-3 text-center {{ $totalOutstanding > 0 ? 'border border-danger' : '' }}">
                <h6 class="text-muted small mb-1">Outstanding </h6>
                <h5 class="fw-bold text-danger mb-0">₹{{ number_format($totalOutstanding, 2) }}</h5>
            </div>
        </div>
    </div>


    {{-- ═══ MAIN TABLE ══════════════════════════════════════════ --}}
    <div class="bg-white rounded shadow-sm p-3">

        <h6 class="fw-bold border-bottom pb-2 mb-3">
            <i class="feather icon-list me-2 text-primary"></i>
            Order-wise Collection Update
        </h6>

        <form action="{{ route('collections.bulk-store') }}" method="POST" id="collection-form">
            @csrf

            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>PO Number</th>
                            <th>SP Name</th>
                            <th>School Name</th>
                            <th class="text-end text-warning"> PO Amount</th>
                            <th class="text-end text-info">Sales/Billing</th>
                            <th class="text-end text-secondary">Pending PO</th>
                            <th class="text-end text-success" style="min-width:140px">Collection</th>
                            <th class="text-end text-danger">Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr id="row-{{ $row->id }}">
                                <td>
                                    <a href="{{ route('invoices.show', $row->id) }}" class="text-primary fw-bold">
                                        {{ $row->po_number }}
                                    </a>
                                </td>
                                <td>{{ $row->user->username }}</td>
                                <td>
                                    {{ $row->customer->name }}
                                    <small class="text-muted d-block">{{ $row->customer->school_code }}</small>
                                </td>
                                <td class="text-end">₹{{ number_format($row->amount, 2) }}</td>
                                <td class="text-end">₹{{ number_format($row->billing_amount, 2) }}</td>
                                <td class="text-end">₹{{ number_format($row->amount - $row->billing_amount, 2) }}</td>

                                {{-- Editable Collection (D) --}}
                                <td class="text-end">
                                    <div class="d-flex align-items-center gap-1 justify-content-end">
                                        <span class="text-muted small">₹</span>
                                        <input type="number" step="0.01" min="0"
                                            name="collections[{{ $row->id }}][amount]"
                                            class="form-control form-control-sm text-end collection-input"
                                            style="width: 110px;" value="{{ $row->collected_amount }}"
                                            max="{{ $row->billing_amount }}" data-invoice-id="{{ $row->id }}"
                                            data-billing="{{ $row->billing_amount }}" placeholder="0.00">
                                        <input type="hidden" name="collections[{{ $row->id }}][invoice_id]"
                                            value="{{ $row->id }}">
                                    </div>
                                    <small class="text-muted d-block text-end mt-1 current-collected"
                                        id="collected-{{ $row->id }}">
                                        Current: ₹{{ number_format($row->collected_amount, 2) }}
                                    </small>
                                </td>

                                {{-- E: Outstanding (auto-calculated) --}}
                                <td class="text-end {{ $row->outstanding_amount > 0 ? 'text-danger fw-bold' : 'text-success' }}"
                                    id="outstanding-{{ $row->id }}">
                                    ₹{{ number_format($row->outstanding_amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No approved orders found for selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if ($rows->count())
                        <tfoot class="table-success fw-bold">
                            <tr>
                                <td colspan="3" class="text-end">Total</td>
                                <td class="text-end">₹{{ number_format($totalPo, 2) }}</td>
                                <td class="text-end">₹{{ number_format($totalBilling, 2) }}</td>
                                <td class="text-end">₹{{ number_format($totalPo - $totalBilling, 2) }}</td>
                                <td class="text-end">₹{{ number_format($totalCollected, 2) }}</td>
                                <td class="text-end">₹{{ number_format($totalOutstanding, 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif

                </table>
            </div>

            {{-- Payment details (shown on any input change) --}}
            <div id="payment-details-section" class="border rounded p-3 mb-3 d-none bg-light">
                <h6 class="fw-bold mb-3">Payment Details</h6>
                <div class="row g-2">
                    <div class="col-lg-3">
                        <label class="form-label small">Payment Mode</label>
                        <select name="payment_mode" class="form-control form-control-sm">
                            <option value="cheque">Cheque</option>
                            <option value="neft">NEFT</option>
                            <option value="upi">UPI</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label small">Transaction / Reference No.</label>
                        <input type="text" name="reference_number" class="form-control form-control-sm"
                            placeholder="Cheque/UTR/UPI ref...">
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label small">Collection Date</label>
                        <input type="date" name="collected_at" class="form-control form-control-sm"
                            value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label small">Remarks</label>
                        <input type="text" name="collection_remarks" class="form-control form-control-sm"
                            placeholder="Optional notes...">
                    </div>
                </div>
            </div>

            @if ($rows->count())
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success"
                        onclick="return confirm('Save collection updates? This will update all dashboards.')">
                        <i class="feather icon-save me-1"></i> Submit Collection Updates
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

            // Show payment details when any amount is changed
            $(document).on('input', '.collection-input', function() {
                const billing = parseFloat($(this).data('billing')) || 0;
                const collected = parseFloat($(this).val()) || 0;
                const outstanding = Math.max(billing - collected, 0);
                const invoiceId = $(this).data('invoice-id');

                // Update outstanding column in real-time
                const outstandingCell = $('#outstanding-' + invoiceId);
                outstandingCell.text('₹' + outstanding.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
                outstandingCell.toggleClass('text-danger fw-bold', outstanding > 0);
                outstandingCell.toggleClass('text-success', outstanding === 0);

                // Show payment details section
                $('#payment-details-section').removeClass('d-none');
            });

        });
    </script>
@endpush
