@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0">New Dispatch</h5>
        <small class="text-muted">
            {{ $invoice->po_number }} — {{ $invoice->customer->name }}
            ({{ $invoice->customer->school_code }})
        </small>
    </div>
    <a href="{{ route('dispatches.index') }}" class="btn btn-sm btn-secondary">
        <i class="feather icon-arrow-left me-1"></i> Back
    </a>
</header>

<form action="{{ route('dispatches.store') }}" method="POST" id="dispatch-form">
    @csrf
    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

    {{-- ═══ SCHOOL & PO INFO ════════════════════════════ --}}
    <div class="bg-white rounded shadow-sm p-4 mb-4">
        <h6 class="fw-bold border-bottom pb-2 mb-3">
            <i class="feather icon-home me-2 text-primary"></i>Delivery Details
        </h6>
        <div class="row">
            <div class="col-lg-4 mb-3">
                <label class="form-label small text-muted">School</label>
                <div class="form-control bg-light">{{ $invoice->customer->name }}</div>
            </div>
            <div class="col-lg-2 mb-3">
                <label class="form-label small text-muted">School Code</label>
                <div class="form-control bg-light">{{ $invoice->customer->school_code }}</div>
            </div>
            <div class="col-lg-3 mb-3">
                <label class="form-label small text-muted">PO Number</label>
                <div class="form-control bg-light">{{ $invoice->po_number }}</div>
            </div>
            <div class="col-lg-3 mb-3">
                <label class="form-label small text-muted">Delivery Due Date</label>
                <div class="form-control bg-light {{ $invoice->delivery_due_date?->isPast() ? 'text-danger' : '' }}">
                    {{ $invoice->delivery_due_date ? $invoice->delivery_due_date->format('d M, Y') : '—' }}
                </div>
            </div>
            <div class="col-lg-12 mb-3">
                <label class="form-label small text-muted">Delivery Address</label>
                <div class="form-control bg-light">
                    {{ $invoice->customer->address }}, {{ $invoice->customer->city }}, {{ $invoice->customer->state }}
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ ITEMS TO DISPATCH ════════════════════════════ --}}
    <div class="bg-white rounded shadow-sm p-4 mb-4">
        <h6 class="fw-bold border-bottom pb-2 mb-3">
            <i class="feather icon-package me-2 text-primary"></i>Items to Dispatch
            <small class="text-muted fw-normal ms-1">Enter quantity being dispatched in this delivery</small>
        </h6>

        <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Category (Product Type)</th>
                    <th>Product</th>
                    <th class="text-end">Ordered</th>
                    <th class="text-end text-success">Dispatched So Far</th>
                    <th class="text-end text-danger">Remaining</th>
                    <th class="text-center" style="min-width:140px">
                        Qty to Dispatch Now <span class="text-danger">*</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $idx => $item)
                <input type="hidden" name="items[{{ $idx }}][invoice_item_id]" value="{{ $item['id'] }}">
                <input type="hidden" name="items[{{ $idx }}][product_id]"      value="{{ $item['product_id'] }}">
                <tr class="{{ $item['remaining_qty'] == 0 ? 'table-secondary' : '' }}">
                    <td class="text-muted small">{{ $idx + 1 }}</td>
                    <td>{{ $item['category_name'] }}</td>
                    <td><strong>{{ $item['product_name'] }}</strong></td>
                    <td class="text-end">{{ $item['ordered_qty'] }}</td>
                    <td class="text-end text-success fw-bold">{{ $item['dispatched_qty'] }}</td>
                    <td class="text-end {{ $item['remaining_qty'] > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                        {{ $item['remaining_qty'] }}
                    </td>
                    <td class="text-center">
                        @if($item['remaining_qty'] > 0)
                        <input type="number"
                               name="items[{{ $idx }}][quantity_dispatched]"
                               class="form-control form-control-sm text-end"
                               min="0"
                               max="{{ $item['remaining_qty'] }}"
                               step="0.01"
                               value="0"
                               placeholder="0">
                        @else
                        <span class="badge bg-success">Fully Dispatched</span>
                        <input type="hidden" name="items[{{ $idx }}][quantity_dispatched]" value="0">
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

    {{-- ═══ TRANSPORT DETAILS (ALL OPTIONAL) ═══════════ --}}
    <div class="bg-white rounded shadow-sm p-4 mb-4">
        <h6 class="fw-bold border-bottom pb-2 mb-3">
            <i class="feather icon-truck me-2 text-primary"></i>
            Transport Details
            <small class="text-muted fw-normal ms-1">— All fields are optional</small>
        </h6>
        <div class="row">
            <div class="col-lg-3 mb-3">
                <label class="form-label">Dispatch Date <span class="text-danger">*</span></label>
                <input type="date" name="dispatch_date" class="form-control"
                       value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-lg-3 mb-3">
                <label class="form-label">Bilty / LR Number</label>
                <input type="text" name="bilty_number" class="form-control"
                       placeholder="Lorry receipt number">
            </div>
            <div class="col-lg-3 mb-3">
                <label class="form-label">Challan Number</label>
                <input type="text" name="challan_number" class="form-control"
                       placeholder="Delivery challan number">
            </div>
            <div class="col-lg-3 mb-3">
                <label class="form-label">Vehicle Number</label>
                <input type="text" name="vehicle_number" class="form-control"
                       placeholder="e.g. MH-12-AB-1234">
            </div>
            <div class="col-lg-3 mb-3">
                <label class="form-label">Driver Name</label>
                <input type="text" name="driver_name" class="form-control" placeholder="Driver's name">
            </div>
            <div class="col-lg-3 mb-3">
                <label class="form-label">Driver Phone</label>
                <input type="text" name="driver_phone" class="form-control" placeholder="10-digit mobile">
            </div>
            <div class="col-lg-6 mb-3">
                <label class="form-label">Remarks</label>
                <input type="text" name="remarks" class="form-control"
                       placeholder="Any notes about this dispatch...">
            </div>
        </div>
    </div>

    {{-- ═══ ACTIONS ════════════════════════════════════ --}}
    <div class="bg-white rounded shadow-sm p-3 d-flex justify-content-between">
        <a href="{{ route('dispatches.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"
                onclick="return confirm('Save this dispatch record?')">
            <i class="feather icon-truck me-1"></i> Record Dispatch
        </button>
    </div>
</form>

@endsection
