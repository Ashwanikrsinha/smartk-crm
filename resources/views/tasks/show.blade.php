@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5 class="d-inline-block me-2 mb-0">Task</h5>
    <a href="{{ route('tasks.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<div class="card border-0 shadow-sm mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-bell me-1 bg-primary text-white rounded p-1"></i>
        Task No. {{ $task->task_number }}
    </header>
  <div class="card-body p-0">
    <section class="table-responsive mb-4">
        <table class="table align-middle" style="min-width: 40rem;">
            <tbody>
                <tr>
                    <th class="ps-3">Title</th>
                    <td style="max-width: 20rem;">{{ $task->title }}</td>
                </tr> 
                <tr>
                    <th class="ps-3">Deadline Time</th>
                    <td>{{$task->deadline_time->format('d M, Y - h:i:s A') }}</td>
                </tr> 
                <tr>
                    <th class="ps-3">Assignee Name</th>
                    <td>
                        {{ $task->assignee->username }}
                    </td>
                </tr> 
                <tr>
                    <th class="ps-3">Assignor Name</th>
                    <td>{{ $task->assignor->username }}</td>
                </tr> 
                <tr>
                    <th class="ps-3">Complete Time</th>
                    <td>{{ isset($task->completed_at) ? $task->completed_at->format('d M, Y - h:i:s A') : 'Pending' }}</td>
                </tr> 
                <tr>
                    <th class="ps-3">Assign Time</th>
                    <td>{{ $task->created_at->format('d M, Y - h:i:s A') }}</td>
                </tr> 
            </tbody>
        </table>
    </section>
  </div>
</div>

 
<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-book me-1 bg-primary text-white rounded p-1"></i> Description
    </header>
    <div class="card-body table-responsive" id="description">
        {!! $task->description ?? 'NOT GIVEN' !!}
    </div>
</div>

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5 class="d-inline-block me-2 mb-0">Comments</h5>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#comment-modal">
      Comment
    </button>
</header>
 
{{-- comment modal --}}
<form action="{{ route('tasks.comments.store', ['task' => $task]) }}" method="POST">
@csrf    
<div class="modal fade" id="comment-modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title">New Comment</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
           <textarea name="comment" id="" cols="30" rows="5" maxlength="250" class="form-control" required></textarea>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
</div>
</form>

@if($comments->count() > 0)
    <section id="comments">
        @foreach ($comments as $comment)
            <div class="card border-0 shadow-sm mb-4">
                <header class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <span>
                        <i class="feather icon-user me-1 bg-primary text-white rounded p-1"></i>
                        <span>{{ $comment->user->username }} &bull; {{ $comment->created_at->format('d M, Y - h:i:s A') }}</span>
                    </span>
                    <div>
                        @can('update', $comment)
                        <a href="{{ route('tasks.comments.edit', ['task' => $task, 'comment' => $comment]) }}" class="btn btn-sm text-primary pe-0"><i class="feather icon-settings"></i></a>
                        @endcan
                        
                        @can('delete', $comment)
                        <form class="d-inline-block"
                            action="{{ route('tasks.comments.destroy', ['task' => $task, 'comment' => $comment]) }}" 
                            method="POST"
                            onsubmit="return confirm('Are You Sure?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
                        </form>
                        @endcan
                    </div>
                </header>
                <div class="card-body">{{ $comment->comment }}</div>
        </div>
        @endforeach
        <div class="d-flex justify-content-end mb-4">{{ $comments->links() }}</div>
    </section>
@else
    <div class="alert bg-white shadow-sm py-3">
        <i class="feather icon-message-circle bg-primary p-1 rounded me-1 text-white"></i>
        No comments
    </div>    
@endif


@endsection
