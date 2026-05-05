@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Warehouse Dashboard</h5>
            <small class="text-muted">Dispatch Queue — Approved Purchase Orders</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dispatches.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="feather icon-list me-1"></i> Dispatch History
            </a>
        </div>
    </header>

    {{-- ═══ FILTERS ════════════════════════════════════════════ --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('dashboard') }}" class="row g-2 align-items-end">
            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">Product Type</label>
                <select name="product_type" id="product_type" class="form-control form-control-sm">
                    <option value="all" {{ $productType == 'all' ? 'selected' : '' }}>All</option>
                    @foreach ($productTypes as $type)
                        <option value="{{ $type->id }}" {{ $productType == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">Product</label>
                <select name="product_id" id="product_id" class="form-control form-control-sm">
                    <option value="all" {{ $productId == 'all' ? 'selected' : '' }}>All</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label small mb-1">Month</label>
                <input type="month" name="month" class="form-control form-control-sm" value="{{ $month ?? '' }}">
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">From Date</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? '' }}">
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">To Date</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? '' }}">
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">From Due Date</label>
                <input type="date" name="due_date_from" class="form-control form-control-sm" value="{{ $duedateFrom ?? '' }}">
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">To Due Date</label>
                <input type="date" name="due_date_to" class="form-control form-control-sm" value="{{ $duedateTo ?? '' }}">
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small mb-1">Year</label>
                <select name="year" class="form-control form-control-sm">
                    @for ($y = date('Y'); $y >= date('Y') - 4; $y--)
                        <option value="{{ $y }}" {{ ($year ?? date('Y')) == $y ? 'selected' : '' }}>
                            {{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-lg-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>

    {{-- ═══ SUMMARY TILES ══════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        <div class="col-md-2">
            <div class="bg-white rounded shadow-sm p-3 text-center border-top border-info border-3">
                <h5>{{ $totalProductTypes }}</h5>
                <small>Total Product Types</small>
            </div>
        </div>

        <div class="col-md-2">
            <div class="bg-white rounded shadow-sm p-3 text-center border-top border-dark border-3">
                <h5>{{ $totalProducts }}</h5>
                <small>Total Products</small>
            </div>
        </div>

        <div class="col-md-2">
            <div class="bg-white rounded shadow-sm p-3 text-center border-top border-primary border-3">
                <h5>{{ number_format($totalOrderedQty) }}</h5>
                <small>Total Units Ordered</small>
            </div>
        </div>

        <div class="col-md-2">
            <div class="bg-white rounded shadow-sm p-3 text-center border-top border-success border-3">
                <h5>{{ $totalDispatchedProducts }}</h5>
                <small>Products Dispatched</small>
            </div>
        </div>

        <div class="col-md-2">
            <div class="bg-white rounded shadow-sm p-3 text-center border-top border-warning border-3">
                <h5>{{ $totalPendingProducts }}</h5>
                <small>Products Pending</small>
            </div>
        </div>

        <div class="col-md-2">
            <div class="bg-white rounded shadow-sm p-3 text-center border-top border-danger border-3">
                <h5>{{ number_format($totalPendingQty) }}</h5>
                <small>Units Pending</small>
            </div>
        </div>

    </div>

    <style>
        .warehouse-table th,
        .warehouse-table td {
            white-space: nowrap;
            padding: 0.5rem 0.75rem !important;
        }

        .col-po {
            min-width: 120px;
        }

        .col-date {
            min-width: 100px;
        }

        .col-school {
            min-width: 200px;
            white-space: normal !important;
        }

        .col-type {
            min-width: 120px;
        }

        .col-product {
            min-width: 180px;
            white-space: normal !important;
        }

        .col-qty {
            min-width: 100px;
        }

        .col-due {
            min-width: 120px;
        }

        .col-action {
            min-width: 100px;
        }
    </style>

    <div class="bg-white rounded shadow-sm p-4">
        <h6 class="fw-bold border-bottom pb-2 mb-3">
            <i class="feather icon-truck me-2 text-primary"></i>
            Orders Pending Dispatch
        </h6>

        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle warehouse-table">
                <thead class="table-light">
                    <tr>
                        <th class="col-po">PO Number</th>
                        <th class="col-date">PO Date</th>
                        <th class="col-school">School</th>
                        <th class="col-type">Product Type</th>
                        <th class="col-product">Product</th>
                        <th class="col-qty text-end">Qty</th>
                        <th class="col-due">Delivery Due Date</th>
                        <th class="col-action text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $invoice)
                        @php
                            $visibleItems = $invoice->invoiceItems->where('remaining_qty', '>', 0);
                            $rowCount = $visibleItems->count();
                        @endphp

                        @foreach ($visibleItems as $item)
                            <tr>
                                @if ($loop->first)
                                    <td rowspan="{{ $rowCount }}">
                                        <a href="{{ route('dispatches.create', ['invoice_id' => $invoice->id]) }}"
                                            class="fw-bold">
                                            {{ $invoice->po_number }}
                                        </a>
                                    </td>
                                    <td rowspan="{{ $rowCount }}">
                                        {{ $invoice->invoice_date->format('d M, Y') }}
                                    </td>
                                    <td rowspan="{{ $rowCount }}">
                                        <div class="fw-bold">{{ $invoice->customer->name }}</div>
                                        <small class="text-muted">{{ $invoice->customer->school_code }}</small>
                                    </td>
                                @endif

                                <td>{{ $item->product->category->name ?? '—' }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td class="text-end">
                                    <div class="text-muted small">Total: {{ (float) $item->quantity }}</div>
                                    <div class="text-danger fw-bold">Left: {{ (float) $item->remaining_qty }}</div>
                                </td>

                                @if ($loop->first)
                                    <td rowspan="{{ $rowCount }}"
                                        class="{{ $invoice->delivery_due_date && $invoice->delivery_due_date->isPast() ? 'text-danger fw-bold' : '' }}">
                                        {{ $invoice->delivery_due_date ? $invoice->delivery_due_date->format('d M, Y') : '—' }}
                                    </td>
                                    <td rowspan="{{ $rowCount }}" class="text-center">
                                        <a href="{{ route('dispatches.create', ['invoice_id' => $invoice->id]) }}"
                                            class="btn btn-xs btn-primary btn-sm">
                                            <i class="feather icon-send me-1"></i> Dispatch
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No approved orders pending.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($rows->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $rows->links() }}
            </div>
        @endif
    </div>
@endsection
