@can('view', $target)
<a href="{{ route('targets.show', ['target' => $target]) }}" class="btn btn-sm text-primary"><i class="feather icon-eye"></i></a>
@endcan

@can('update', $target)
<a href="{{ route('targets.edit', ['target' => $target]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('delete', $target)
<form class="d-inline-block" action="{{ route('targets.destroy', ['target' => $target]) }}" method="POST"
onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan