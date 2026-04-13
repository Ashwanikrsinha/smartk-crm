@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>New Leave</h5>
    <a href="{{ route('leaves.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('leaves.store') }}" method="POST" 
   class="p-3 shadow-sm rounded bg-white"
   onsubmit="return confirm('Are you sure?');">
    @include('leaves.form', ['mode' => 'create'])
</form>
@endsection

