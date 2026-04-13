@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>New Task</h5>
    <a href="{{ route('tasks.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('tasks.store') }}" method="POST" class="p-3 shadow-sm rounded bg-white"
   onsubmit="return confirm('Are you sure?');">
    @include('tasks.form', ['mode' => 'create'])
</form>

@endsection

