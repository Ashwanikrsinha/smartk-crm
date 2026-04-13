@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>New Visit</h5>
    <a href="{{ route('visits.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('visits.store') }}" method="POST" class="p-3 shadow-sm rounded bg-white">
    @include('visits.form', ['mode' => 'create'])
</form>

@include('customers.modal')

@endsection

