@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>Edit Permission</h5>
    <a href="{{ route('permissions.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('permissions.update', ['permission' => $permission ]) }}"
     method="POST" class="p-3 shadow-sm rounded bg-white">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" value="{{ $permission->name }}" required>
    </div>

    <button type="submit" class="btn btn-primary">Edit</button>
</form>

@endsection