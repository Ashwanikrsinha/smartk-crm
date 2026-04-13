@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Notifications</h5>
  
  @if ($notifications->count() > 0)
   <button class="btn btn-primary" id="mark-all">Mark all as Read</button>
   @else
   <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">Back</a>
  @endif
</header>

@if ($notifications->count() > 0)
    
    @foreach ($notifications as $notification)
        <div class="card border-0 shadow-sm mb-4">
        <header class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <span>
                <i class="feather icon-bell me-1 bg-primary text-white rounded p-1"></i>
                @if ($notification->type == App\Notifications\NewTaskNotification::class)
                    <span class="badge bg-secondary">Task</span>
                @elseif ($notification->type == App\Notifications\NewTaskCommentNotification::class)
                    <span class="badge bg-secondary">Comment</span>
                @elseif ($notification->type == App\Notifications\NewLeaveNotification::class
                        || $notification->type == App\Notifications\LeaveUpdateNotification::class)
                    <span class="badge bg-secondary">Leave</span>
                @endif
                &bull;
                {{ $notification->created_at->format('d M, Y - h:i:s A') }}
            </span>
            <button class="btn btn-sm text-primary mark-as-read d-none d-md-inline-block"
                data-id="{{ $notification->id }}">
                Mark as Read
            </button>
        </header>
        
        <div class="card-body">
           @if ($notification->type == App\Notifications\NewTaskNotification::class)
                
                <span class="text-primary">{{ $notification->data['assignor_name'] }}</span> assigned,  
                Task No. 
                <a href="{{ route('tasks.show', ['task' => $notification->data['id'] ]) }}">
                    {{ $notification->data['task_number'] }}
                </a>

           @elseif ($notification->type == App\Notifications\NewTaskCommentNotification::class)
    
            <span class="text-primary">{{ $notification->data['comment_by'] }}</span> commented on,  
            Task No. 
            <a href="{{ route('tasks.show', ['task' => $notification->data['id'] ]) }}">
                {{ $notification->data['task_number'] }}
            </a>

           @elseif ($notification->type == App\Notifications\NewLeaveNotification::class)
        
            <span class="text-primary">{{ $notification->data['leave_by'] }}</span> applied for,  
            Leave No. 
            <a href="{{ route('leaves.show', ['leave' => $notification->data['id'] ]) }}">
                {{ $notification->data['leave_number'] }}
            </a>

            @elseif ($notification->type == App\Notifications\LeaveUpdateNotification::class)
        
            <span class="text-primary">{{ $notification->data['leave_by'] }}</span> your,  
            Leave No.
            <a href="{{ route('leaves.show', ['leave' => $notification->data['id'] ]) }}">
                {{ $notification->data['leave_number'] }}
            </a>
            updated and status is <span class="text-primary">{{ ucwords($notification->data['status']) }}</span>

           @endif
        </div>
    </div>
    @endforeach

@else
    <div class="alert bg-white shadow-sm py-3">
        <i class="feather icon-bell bg-primary p-1 rounded me-1 text-white"></i>
        No new notifications
    </div>
@endif


@endsection


@push('scripts')
<script>
  
  function sendMarkRequest(id = null) {
        return $.ajax("{{ route('notifications.mark') }}", {
            method: 'POST',
            data: {
                _token : `{{ csrf_token() }}`,
                id
            }
        });
    }


  $(document).ready(() => {

    $('.mark-as-read').click(function() {
        let request = sendMarkRequest($(this).data('id'));
        request.done(() => {
            $(this).parent().parent().fadeOut(500);
        });
    });

    $('#mark-all').click(function() {
        let request = sendMarkRequest();
        request.done(() => {
            $('.card').fadeOut(500);
        })
    });

    });   
</script>
@endpush