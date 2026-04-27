<a href="{{ route('leaves.show', $leave) }}" class="btn btn-sm text-primary" title="View">
    <i class="feather icon-eye"></i>
</a>

{{-- SP can edit their own pending leaves --}}
@if (auth()->id() === $leave->user_id && $leave->isPending())
    <a href="{{ route('leaves.edit', $leave) }}" class="btn btn-sm text-primary" title="Edit">
        <i class="feather icon-edit-2"></i>
    </a>

    <form class="d-inline-block" action="{{ route('leaves.destroy', $leave) }}" method="POST"
        onsubmit="return confirm('Cancel this leave application?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm text-danger" title="Cancel Leave">
            <i class="feather icon-trash-2"></i>
        </button>
    </form>
@endif

{{-- SM sees approve/reject shortcut for pending --}}
@if (
    $leave->isPending() &&
        (auth()->user()->isSalesManager() || auth()->user()->isAdmin()) &&
        auth()->id() !== $leave->user_id)
    <a href="{{ route('leaves.show', $leave) }}" class="btn btn-sm btn-outline-warning btn-sm">
        Review
    </a>
@endif
