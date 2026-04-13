@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>Edit Role</h5>
    <a href="{{ route('roles.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('roles.update', ['role' => $role ]) }}" method="POST"
    class="p-3 shadow-sm rounded bg-white">
    @method('PUT')
    @include('roles.form', ['mode' => 'edit'])
</form>

@endsection