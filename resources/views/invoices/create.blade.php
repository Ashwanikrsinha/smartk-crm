@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">New Purchase Order</h5>
            <small class="text-muted">Fill in the school and order details below</small>
        </div>
        <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-secondary">
            <i class="feather icon-arrow-left me-1"></i> Back
        </a>
    </header>

    <form action="{{ route('invoices.store') }}" method="POST" id="po-form" enctype="multipart/form-data">
        @include('invoices.form', ['mode' => 'create'])
    </form>

    {{-- New School Modal --}}
    @include('invoices.partials.new-school-modal')
@endsection
