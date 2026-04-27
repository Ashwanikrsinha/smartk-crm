@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between mb-4 align-items-center">
        <h5>Register New School</h5>
        <a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary">Back</a>
    </header>

    {{-- enctype REQUIRED for document file uploads --}}
    <form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data"
        class="p-3 shadow-sm rounded bg-white">
        @include('customers.form', ['mode' => 'create'])
    </form>
@endsection
