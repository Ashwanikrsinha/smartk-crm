@can('update', $state)
<a href="{{ route('states.edit', ['state' => $state]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan
@can('delete', $state)
<form class="d-inline-block" action="{{ route('states.destroy', ['state' => $state]) }}" method="POST"
 onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan