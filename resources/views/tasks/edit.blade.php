@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
      <h5 class="d-inline-block me-2 mb-0">Edit Task</h5>
    <a href="{{ route('tasks.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('tasks.update', ['task' => $task]) }}" method="POST" 
    class="p-3 shadow-sm rounded bg-white"
    onsubmit="return confirm('Are you sure?');">
    @method('PUT')
    @include('tasks.form', ['mode' => 'edit'])
</form>

@endsection
