@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>New Transport</h5>
    <a href="{{ route('transports.index') }}" class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('transports.store') }}" method="POST" class="p-3 bg-white rounded shadow-sm">
    @include('transports.form', ['mode' => 'create'])
</form>
@endsection

