@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Edit School</h5>
            <small class="text-muted">{{ $customer->school_code }} — {{ $customer->name }}</small>
        </div>
        <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-secondary">
            <i class="feather icon-arrow-left me-1"></i> Back
        </a>
    </header>

    <form action="{{ route('customers.update', $customer) }}" method="POST" enctype="multipart/form-data"
        class="bg-white rounded shadow-sm p-4">
        @method('PUT')
        @include('customers.form', ['mode' => 'edit'])
    </form>
@endsection
