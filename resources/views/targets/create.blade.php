@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>New Visit Target</h5>
    <a href="{{ route('targets.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('targets.store') }}" method="POST" class="p-3 shadow-sm rounded bg-white"
   onsubmit="return confirm('Are you sure?');">
    @include('targets.form', ['mode' => 'create'])
</form>

@endsection

