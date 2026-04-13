@can('view', $visit)
<a href="{{ route('visits.show', ['visit' => $visit]) }}" class="btn btn-sm text-primary"><i class="feather icon-eye"></i></a>
@endcan

@can('update', $visit)
<a href="{{ route('visits.edit', ['visit' => $visit]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('delete', $visit)
<form class="d-inline-block" action="{{ route('visits.destroy', ['visit' => $visit]) }}" method="POST"
onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan
