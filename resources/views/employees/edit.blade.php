@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>Edit Employee</h5>
    <a href="{{ route('employees.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('employees.update', ['employee' => $employee]) }}" method="POST" class="p-3 shadow-sm rounded bg-white">
	@method('PUT')
    @include('employees.form', ['mode' => 'edit'])
</form>
@endsection
