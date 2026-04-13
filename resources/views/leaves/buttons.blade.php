@can('view', $leave)
<a href="{{ route('leaves.show', ['leave' => $leave]) }}" class="btn btn-sm text-primary"><i class="feather icon-eye"></i></a>
@endcan

@can('update', $leave)
<a href="{{ route('leaves.edit', ['leave' => $leave]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('delete', $leave)
<form class="d-inline-block" action="{{ route('leaves.destroy', ['leave' => $leave]) }}" method="POST"
onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan
