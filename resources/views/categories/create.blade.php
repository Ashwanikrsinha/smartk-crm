@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>New Category</h5>
    <a href="{{ route('categories.index') }}" class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('categories.store') }}" method="POST"
class="p-3 bg-white rounded shadow-sm">
    @csrf
    <div class="mb-3">
        <label for="" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" required>
    </div>
    <button type="submit" class="btn btn-primary ">Save</button>
</form>

@endsection