@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>Edit Unit</h5>
    <a href="{{ route('units.index') }}" class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('units.update', ['unit' => $unit ]) }}" method="POST"
    class="p-3 bg-white rounded shadow-sm">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" value="{{ $unit->name }}" required>
    </div>

    <div class="mb-3">
        <label for="" class="form-label">HSN Code</label>
        <input type="text" class="form-control" name="code" value="{{ $unit->code }}" required>
    </div>

    <button type="submit" class="btn btn-primary ">Edit</button>
</form>

@endsection