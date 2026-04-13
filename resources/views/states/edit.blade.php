@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>Edit State</h5>
    <a href="{{ route('states.index') }}" class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('states.update', ['state' => $state ]) }}" method="POST" class="p-3 bg-white rounded shadow-sm">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" value="{{ $state->name }}" required>
    </div>

    <button type="submit" class="btn btn-primary ">Edit</button>
</form>

@endsection