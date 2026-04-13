@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>Edit User</h5>
    <a href="{{ route('users.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('users.update', ['user' => $user]) }}" method="POST" class="p-3 shadow-sm rounded bg-white">
	@method('PUT')
    @include('users.form', ['mode' => 'edit'])
</form>
@endsection
