@if (auth()->user()->isAdmin())
    <a href="{{ route('lead-sources.edit', $leadSource) }}" class="btn btn-sm text-primary" title="Edit">
        <i class="feather icon-edit-2"></i>
    </a>

    <form class="d-inline-block" action="{{ route('lead-sources.destroy', $leadSource) }}" method="POST"
        onsubmit="return confirm('Delete \'{{ addslashes($leadSource->name) }}\'? Schools linked to it will lose their lead source.')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm text-danger" title="Delete">
            <i class="feather icon-trash-2"></i>
        </button>
    </form>
@endif
