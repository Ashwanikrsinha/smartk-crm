@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0">Dispatch History</h5>
        <small class="text-muted">
            {{ $invoice->po_number }} — {{ $invoice->customer->name }}
            <span class="badge bg-secondary">{{ $invoice->customer->school_code }}</span>
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

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@forelse($dispatches as $dispatch)
<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="bg-white rounded shadow-sm p-4 h-100">
            <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-3">
                <h6 class="fw-bold mb-0">{{ $dispatch->dispatch_number }}</h6>

                @if($canEdit)
                    @if(!$dispatch->isEdited())
                        <button type="button" class="btn btn-sm btn-outline-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#editDispatchModal{{ $dispatch->id }}">
                            <i class="feather icon-edit-2 me-1"></i> Edit
                        </button>
                    @else
                        <span class="badge bg-light text-muted border">Edited — locked</span>
                    @endif
                @endif
            </div>

            <table class="table table-sm table-borderless mb-0">
                <tr><th class="text-muted" style="width:40%">Dispatch Date</th>
                    <td>{{ $dispatch->dispatch_date->format('d M, Y') }}</td></tr>
                <tr><th class="text-muted">Dispatched By</th>
                    <td>{{ $dispatch->dispatchedBy->username ?? '—' }}</td></tr>
                <tr><th class="text-muted">Bilty / LR No.</th>
                    <td>{{ $dispatch->bilty_number ?? '—' }}</td></tr>
                <tr><th class="text-muted">Challan No.</th>
                    <td>{{ $dispatch->challan_number ?? '—' }}</td></tr>
                <tr><th class="text-muted">Vehicle No.</th>
                    <td>{{ $dispatch->vehicle_number ?? '—' }}</td></tr>
                <tr><th class="text-muted">Driver</th>
                    <td>{{ $dispatch->driver_name ?? '—' }}
                        @if($dispatch->driver_phone)
                        <small class="text-muted d-block">{{ $dispatch->driver_phone }}</small>
                        @endif
                    </td></tr>
                @if($dispatch->remarks)
                <tr><th class="text-muted">Remarks</th>
                    <td>{{ $dispatch->remarks }}</td></tr>
                @endif

                @if($dispatch->isEdited())
                <tr><th class="text-muted">Edited By</th>
                    <td>
                        {{ $dispatch->editedBy->username ?? '—' }}
                        <small class="text-muted d-block">
                            {{ $dispatch->edited_at?->format('d M, Y h:i A') }}
                        </small>
                    </td></tr>
                @endif
            </table>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="bg-white rounded shadow-sm p-4 h-100">
            <h6 class="fw-bold border-bottom pb-2 mb-3">Items Dispatched</h6>
            <table class="table table-bordered table-sm mb-0">
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

{{-- ═══ EDIT MODAL — only rendered if this dispatch is still editable ═══ --}}
@if($canEdit && !$dispatch->isEdited())
<div class="modal fade" id="editDispatchModal{{ $dispatch->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('dispatches.update', $dispatch) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Edit {{ $dispatch->dispatch_number }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning small">
                        <i class="feather icon-alert-triangle me-1"></i>
                        This dispatch can only be corrected <strong>once</strong>.
                        After saving, it will be locked from further edits.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dispatch Date <span class="text-danger">*</span></label>
                        <input type="date" name="dispatch_date" class="form-control"
                               value="{{ $dispatch->dispatch_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bilty / LR Number</label>
                        <input type="text" name="bilty_number" class="form-control"
                               value="{{ $dispatch->bilty_number }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Challan Number</label>
                        <input type="text" name="challan_number" class="form-control"
                               value="{{ $dispatch->challan_number }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vehicle Number</label>
                        <input type="text" name="vehicle_number" class="form-control"
                               value="{{ $dispatch->vehicle_number }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Driver Name</label>
                        <input type="text" name="driver_name" class="form-control"
                               value="{{ $dispatch->driver_name }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Driver Phone</label>
                        <input type="text" name="driver_phone" class="form-control"
                               value="{{ $dispatch->driver_phone }}">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" class="form-control"
                               value="{{ $dispatch->remarks }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"
                            onclick="return confirm('Save changes? This dispatch can only be edited once.')">
                        Save Correction
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

@empty
<div class="bg-white rounded shadow-sm p-4 text-center text-muted">
    No dispatches recorded for this PO yet.
</div>
@endforelse

@endsection