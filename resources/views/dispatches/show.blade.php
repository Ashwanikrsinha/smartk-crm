@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0">{{ $dispatch->dispatch_number }}</h5>
        <small class="text-muted">
            {{ $dispatch->dispatch_date->format('d M, Y') }} —
            {{ $dispatch->invoice->po_number }} — {{ $dispatch->invoice->customer->name }}
        </small>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
            <i class="feather icon-printer me-1"></i> Print
        </button>
        <a href="{{ route('dispatches.index') }}" class="btn btn-sm btn-secondary">
            <i class="feather icon-arrow-left me-1"></i> Back
        </a>
    </div>
</header>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="bg-white rounded shadow-sm p-4 mb-3">
            <h6 class="fw-bold border-bottom pb-2 mb-3">Dispatch Details</h6>
            <table class="table table-sm table-borderless">
                <tr><th class="text-muted" style="width:40%">Dispatch No.</th>
                    <td><strong>{{ $dispatch->dispatch_number }}</strong></td></tr>
                <tr><th class="text-muted">Dispatch Date</th>
                    <td>{{ $dispatch->dispatch_date->format('d M, Y') }}</td></tr>
                <tr><th class="text-muted">PO Number</th>
                    <td><a href="{{ route('invoices.show', $dispatch->invoice) }}">{{ $dispatch->invoice->po_number }}</a></td></tr>
                <tr><th class="text-muted">School</th>
                    <td>{{ $dispatch->invoice->customer->name }}<br>
                        <small class="text-muted">{{ $dispatch->invoice->customer->school_code }}</small></td></tr>
                <tr><th class="text-muted">Dispatched By</th>
                    <td>{{ $dispatch->dispatchedBy->username }}</td></tr>
                @if($dispatch->bilty_number)
                <tr><th class="text-muted">Bilty / LR No.</th>
                    <td>{{ $dispatch->bilty_number }}</td></tr>
                @endif
                @if($dispatch->challan_number)
                <tr><th class="text-muted">Challan No.</th>
                    <td>{{ $dispatch->challan_number }}</td></tr>
                @endif
                @if($dispatch->vehicle_number)
                <tr><th class="text-muted">Vehicle No.</th>
                    <td>{{ $dispatch->vehicle_number }}</td></tr>
                @endif
                @if($dispatch->driver_name)
                <tr><th class="text-muted">Driver</th>
                    <td>{{ $dispatch->driver_name }}
                        @if($dispatch->driver_phone)
                        <small class="text-muted d-block">{{ $dispatch->driver_phone }}</small>
                        @endif
                    </td></tr>
                @endif
                @if($dispatch->remarks)
                <tr><th class="text-muted">Remarks</th>
                    <td>{{ $dispatch->remarks }}</td></tr>
                @endif
            </table>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="bg-white rounded shadow-sm p-4">
            <h6 class="fw-bold border-bottom pb-2 mb-3">Items Dispatched</h6>
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Category</th>
                        <th>Product</th>
                        <th class="text-end">Qty Dispatched</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dispatch->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->product->category?->name ?? '—' }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-end fw-bold">{{ $item->quantity_dispatched }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">Total Units Dispatched</td>
                        <td class="text-end">{{ $dispatch->items->sum('quantity_dispatched') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection
