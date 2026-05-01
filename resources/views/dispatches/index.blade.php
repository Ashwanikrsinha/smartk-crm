@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0">Warehouse Dashboard</h5>
        <small class="text-muted">Pending dispatches for approved purchase orders</small>
    </div>
    <a href="{{ route('dispatches.create.select') }}" class="btn btn-primary btn-sm">
        <i class="feather icon-truck me-1"></i> New Dispatch
    </a>
</header>

{{-- ═══ SUMMARY WIDGETS ════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="bg-white rounded shadow-sm p-3 text-center">
            <div class="text-primary mb-1"><i class="feather icon-file-text fs-4"></i></div>
            <h4 class="fw-bold mb-0">{{ $approvedPos->count() }}</h4>
            <small class="text-muted">Approved POs</small>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="bg-white rounded shadow-sm p-3 text-center">
            <div class="text-warning mb-1"><i class="feather icon-clock fs-4"></i></div>
            <h4 class="fw-bold mb-0">{{ $approvedPos->filter(fn($p) => $p->dispatches->isEmpty())->count() }}</h4>
            <small class="text-muted">Not Yet Dispatched</small>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="bg-white rounded shadow-sm p-3 text-center">
            <div class="text-success mb-1"><i class="feather icon-truck fs-4"></i></div>
            <h4 class="fw-bold mb-0">{{ $approvedPos->filter(fn($p) => $p->dispatches->isNotEmpty())->count() }}</h4>
            <small class="text-muted">Partially / Fully Dispatched</small>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="bg-white rounded shadow-sm p-3 text-center">
            <div class="text-info mb-1"><i class="feather icon-package fs-4"></i></div>
            <h4 class="fw-bold mb-0">
                {{ $approvedPos->sum(fn($p) => $p->dispatches->count()) }}
            </h4>
            <small class="text-muted">Total Dispatches Made</small>
        </div>
    </div>
</div>

{{-- ═══ PO TABLE — NO FINANCIAL DATA ══════════════════════ --}}
<div class="bg-white rounded shadow-sm p-3">

    <h6 class="fw-bold border-bottom pb-2 mb-3">
        <i class="feather icon-list me-2 text-primary"></i>
        Approved Orders — Dispatch Status
    </h6>

    <div class="table-responsive">
    <table class="table table-bordered table-sm table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>PO Number</th>
                <th>PO Date</th>
                <th>School</th>
                <th>School Code</th>
                <th>Category (Product Type)</th>
                <th>Products</th>
                <th class="text-end">Ordered Qty</th>
                <th class="text-end">Dispatched</th>
                <th class="text-end">Remaining</th>
                <th>Delivery Due</th>
                <th>Dispatches</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($approvedPos as $po)
            @php
                $dispatched = \App\Models\DispatchItem::whereHas('dispatch',
                    fn($q) => $q->where('invoice_id', $po->id))
                    ->selectRaw('invoice_item_id, SUM(quantity_dispatched) as done')
                    ->groupBy('invoice_item_id')
                    ->get()->keyBy('invoice_item_id');

                $rowCount = $po->invoiceItems->count();
            @endphp

            @foreach($po->invoiceItems as $idx => $item)
            <tr>
                {{-- PO Number — show only on first item row --}}
                @if($idx === 0)
                <td rowspan="{{ $rowCount }}" class="fw-bold text-primary">
                    <a href="{{ route('dispatches.po-summary', $po->id) }}" class="text-primary">
                        {{ $po->po_number }}
                    </a>
                </td>
                <td rowspan="{{ $rowCount }}">{{ $po->invoice_date->format('d M, Y') }}</td>
                <td rowspan="{{ $rowCount }}">{{ $po->customer->name }}</td>
                <td rowspan="{{ $rowCount }}">
                    <span class="badge bg-secondary">{{ $po->customer->school_code }}</span>
                </td>
                @endif

                {{-- Per-item columns --}}
                <td>{{ $item->product->category?->name ?? '—' }}</td>
                <td>{{ $item->product->name }}</td>
                <td class="text-end">{{ $item->quantity }}</td>

                @php
                    $done      = $dispatched[$item->id]->done ?? 0;
                    $remaining = max($item->quantity - $done, 0);
                    $fullyDone = $remaining == 0;
                @endphp

                <td class="text-end text-success fw-bold">{{ $done }}</td>
                <td class="text-end {{ $remaining > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                    {{ $remaining }}
                </td>

                @if($idx === 0)
                <td rowspan="{{ $rowCount }}">
                    @if($po->delivery_due_date)
                        @php $due = $po->delivery_due_date; @endphp
                        <span class="{{ $due->isPast() ? 'text-danger fw-bold' : 'text-dark' }}">
                            {{ $due->format('d M, Y') }}
                        </span>
                        @if($due->isPast())
                        <span class="badge bg-danger d-block mt-1">Overdue</span>
                        @elseif($due->diffInDays(now()) <= 7)
                        <span class="badge bg-warning text-dark d-block mt-1">Due Soon</span>
                        @endif
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td rowspan="{{ $rowCount }}" class="text-center">
                    @if($po->dispatches->count() > 0)
                    <span class="badge bg-info">{{ $po->dispatches->count() }} dispatch(es)</span>
                    <a href="{{ route('dispatches.po-summary', $po->id) }}"
                       class="btn btn-xs btn-link p-0 d-block mt-1 text-info">
                        View details
                    </a>
                    @else
                    <span class="badge bg-warning text-dark">None yet</span>
                    @endif
                </td>
                <td rowspan="{{ $rowCount }}" class="text-center">
                    <a href="{{ route('dispatches.create', ['invoice_id' => $po->id]) }}"
                       class="btn btn-sm btn-outline-primary">
                        <i class="feather icon-truck me-1"></i> Dispatch
                    </a>
                </td>
                @endif
            </tr>
            @endforeach

            @empty
            <tr>
                <td colspan="12" class="text-center text-muted py-4">No approved orders pending dispatch.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@endsection
