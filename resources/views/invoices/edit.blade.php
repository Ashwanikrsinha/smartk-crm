@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Edit Purchase Order</h5>
            <small class="text-muted">
                {{ $invoice->po_number }}
                <span class="badge bg-{{ $invoice->isRejected() ? 'danger' : 'warning' }} ms-1">
                    {{ ucfirst($invoice->status) }}
                </span>
            </small>
        </div>
        <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-secondary">
            <i class="feather icon-arrow-left me-1"></i> Back
        </a>
    </header>

    {{-- Show rejection reason if returned by SM --}}
    @if ($invoice->isRejected() && $invoice->rejection_reason)
        <div class="alert alert-danger mb-4">
            <i class="feather icon-alert-circle me-2"></i>
            <strong>Rejected by Sales Manager:</strong> {{ $invoice->rejection_reason }}
        </div>
    @endif

    <form action="{{ route('invoices.update', $invoice) }}" method="POST" id="po-form" enctype="multipart/form-data">
        @method('PUT')
        @include('invoices.form', ['mode' => 'edit'])
    </form>

    @include('invoices.partials.new-school-modal')
@endsection
