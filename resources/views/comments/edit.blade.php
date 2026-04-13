@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>Edit Task Comment</h5>
    <a href="{{ route('tasks.show', ['task' => $task ]) }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('tasks.comments.update', ['task' => $task, 'comment' => $comment ]) }}" method="POST"
    class="p-3 shadow-sm rounded bg-white">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="" class="form-label">Comment</label>
        <textarea name="comment" id="" cols="30" rows="5" maxlength="250" class="form-control" required>{{ $comment->comment }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Edit</button>
</form>

@endsection