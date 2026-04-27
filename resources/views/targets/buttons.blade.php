<a href="{{ route('targets.show', $target) }}" class="btn btn-sm text-primary" title="View">
    <i class="feather icon-eye"></i>
</a>

@if (auth()->user()->isSalesManager() || auth()->user()->isAdmin())
    <a href="{{ route('targets.edit', $target) }}" class="btn btn-sm text-primary" title="Edit">
        <i class="feather icon-edit-2"></i>
    </a>

    <form class="d-inline-block" action="{{ route('targets.destroy', $target) }}" method="POST"
        onsubmit="return confirm('Delete this target?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm text-danger" title="Delete">
            <i class="feather icon-trash-2"></i>
        </button>
    </form>
@endif
