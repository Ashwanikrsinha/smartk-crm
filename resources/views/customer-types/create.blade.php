@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>New Customer Type</h5>
    <a href="{{ route('customer-types.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('customer-types.store') }}" method="POST" 
class="p-3 shadow-sm rounded bg-white">
    @csrf
    <div class="mb-3">
        <label for="" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" value="{{ old('name') }}">
    </div>
    
    <button type="submit" class="btn btn-primary">Save</button>

</form>

@endsection