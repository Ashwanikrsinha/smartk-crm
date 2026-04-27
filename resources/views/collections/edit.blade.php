@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Edit Collection Entry</h5>
            <small class="text-muted">
                PO: {{ $collection->invoice->po_number }} —
                {{ $collection->invoice->customer->name }}
            </small>
        </div>
        <a href="{{ route('collections.index') }}" class="btn btn-sm btn-secondary">Back</a>
    </header>

    <form action="{{ route('collections.update', $collection) }}" method="POST" class="bg-white rounded shadow-sm p-4"
        style="max-width: 600px;">
        @csrf
        @method('PUT')

        <div class="row">

            <div class="col-lg-6 mb-3">
                <label class="form-label">Collected Amount (₹) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0.01" name="collected_amount" class="form-control"
                    value="{{ old('collected_amount', $collection->collected_amount) }}" required>
            </div>

            <div class="col-lg-6 mb-3">
                <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                <select name="payment_mode" class="form-control" required>
                    @foreach (['cheque', 'neft', 'upi', 'cash'] as $mode)
                        <option value="{{ $mode }}" {{ $collection->payment_mode === $mode ? 'selected' : '' }}>
                            {{ strtoupper($mode) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-6 mb-3">
                <label class="form-label">Collection Date <span class="text-danger">*</span></label>
                <input type="date" name="collected_at" class="form-control"
                    value="{{ old('collected_at', $collection->collected_at->format('Y-m-d')) }}" required>
            </div>

            <div class="col-lg-6 mb-3">
                <label class="form-label">Reference / Cheque No.</label>
                <input type="text" name="reference_number" class="form-control"
                    value="{{ old('reference_number', $collection->reference_number) }}"
                    placeholder="Cheque/UTR/UPI reference">
            </div>

            <div class="col-12 mb-3">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="2" placeholder="Optional notes...">{{ old('remarks', $collection->remarks) }}</textarea>
            </div>

        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="feather icon-save me-1"></i> Update
            </button>
            <a href="{{ route('collections.index') }}" class="btn btn-secondary">Cancel</a>
        </div>

    </form>
@endsection
