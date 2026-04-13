@can('update', $event)
<a href="{{ route('events.edit', ['event' => $event]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan
@can('delete', $event)
<form class="d-inline-block" action="{{ route('events.destroy', ['event' => $event]) }}" method="POST"
 onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan