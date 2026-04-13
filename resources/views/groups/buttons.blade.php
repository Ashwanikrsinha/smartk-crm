@can('update', $group)
<a href="{{ route('groups.edit', ['group' => $group]) }}" class="btn text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('delete', $group)
    <form class="d-inline-block" action="{{ route('groups.destroy', ['group' => $group]) }}" method="POST"
    onsubmit="return confirm('Are You Sure?')">
        @csrf
        @method('DELETE')
        <button class="btn"><i class="feather icon-trash-2 text-primary"></i></button>
    </form>
@endcan