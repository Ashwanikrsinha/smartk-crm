@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
      <h5 class="d-inline-block me-2 mb-0">Edit Leave </h5>
    <a href="{{ route('leaves.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('leaves.update', ['leave' => $leave]) }}" 
    method="POST" 
    class="p-3 shadow-sm rounded bg-white"
    onsubmit="return confirm('Are you sure?');">
    @method('PUT')
    @include('leaves.form', ['mode' => 'edit'])
</form>

@endsection