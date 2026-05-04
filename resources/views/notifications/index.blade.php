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
        <div class="card border-0 shadow-sm mb-4 {{ $notification->unread() ? 'border-start border-4 border-primary' : '' }}">
        <header class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <span>
                <i class="feather icon-bell me-1 {{ $notification->unread() ? 'bg-primary' : 'bg-secondary' }} text-white rounded p-1"></i>
                @if ($notification->type == 'App\Notifications\NewTaskNotification')
                    <span class="badge bg-secondary">Task</span>
                @elseif ($notification->type == 'App\Notifications\NewTaskCommentNotification')
                    <span class="badge bg-secondary">Comment</span>
                @elseif ($notification->type == 'App\Notifications\NewLeaveNotification'
                        || $notification->type == 'App\Notifications\LeaveUpdateNotification'
                        || $notification->type == 'App\Notifications\LeaveRequested'
                        || $notification->type == 'App\Notifications\LeaveDecided')
                    <span class="badge bg-secondary">Leave</span>
                @elseif ($notification->type == 'App\Notifications\PoApproved'
                        || $notification->type == 'App\Notifications\PoRejected')
                    <span class="badge bg-secondary">PO Order</span>
                @endif
                &bull;
                {{ $notification->created_at->format('d M, Y - h:i:s A') }}
            </span>
            @if($notification->unread())
            <button class="btn btn-sm text-primary mark-as-read d-none d-md-inline-block"
                data-id="{{ $notification->id }}">
                Mark as Read
            </button>
            @endif
        </header>

        <div class="card-body">
           @if ($notification->type == 'App\Notifications\NewTaskNotification')

                <span class="text-primary">{{ $notification->data['assignor_name'] ?? 'System' }}</span> assigned,
                Task No.
                <a href="{{ route('tasks.show', ['task' => $notification->data['id'] ]) }}">
                    {{ $notification->data['task_number'] }}
                </a>

           @elseif ($notification->type == 'App\Notifications\NewTaskCommentNotification')

            <span class="text-primary">{{ $notification->data['comment_by'] ?? 'User' }}</span> commented on,
            Task No.
            <a href="{{ route('tasks.show', ['task' => $notification->data['id'] ]) }}">
                {{ $notification->data['task_number'] }}
            </a>

           @elseif ($notification->type == 'App\Notifications\NewLeaveNotification' || $notification->type == 'App\Notifications\LeaveRequested')

            <span class="text-primary">{{ $notification->data['leave_by'] ?? 'User' }}</span> applied for,
            Leave No.
            <a href="{{ route('leaves.show', ['leave' => $notification->data['id'] ?? 0 ]) }}">
                {{ $notification->data['leave_number'] ?? 'N/A' }}
            </a>

            @elseif ($notification->type == 'App\Notifications\LeaveUpdateNotification' || $notification->type == 'App\Notifications\LeaveDecided')

            <span class="text-primary">{{ $notification->data['leave_by'] ?? 'User' }}</span> your,
            Leave No.
            <a href="{{ route('leaves.show', ['leave' => $notification->data['id'] ?? 0 ]) }}">
                {{ $notification->data['leave_number'] ?? 'N/A' }}
            </a>
            updated and status is <span class="text-primary">{{ ucwords($notification->data['status'] ?? 'Updated') }}</span>

           @elseif (in_array($notification->type, ['App\Notifications\PoApproved', 'App\Notifications\PoRejected']))
                <h6 class="fw-bold mb-1">{{ $notification->data['title'] ?? 'PO Update' }}</h6>
                <p class="mb-0">{{ $notification->data['message'] ?? '' }}</p>
                @if(isset($notification->data['url']))
                    <a href="{{ $notification->data['url'] }}" class="btn btn-xs btn-outline-primary mt-2">View PO Details</a>
                @endif
           @else
                {{-- Fallback for any other notification types --}}
                @if(isset($notification->data['message']))
                    {{ $notification->data['message'] }}
                @elseif(isset($notification->data['title']))
                    {{ $notification->data['title'] }}
                @else
                    You have a new notification.
                @endif
           @endif
        </div>
    </div>
    @endforeach

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>

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
