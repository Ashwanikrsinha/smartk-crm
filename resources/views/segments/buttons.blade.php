@can('update', $segment)
<a href="{{ route('segments.edit', ['segment' => $segment]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('delete', $segment)
<form class="d-inline-block" action="{{ route('segments.destroy', ['segment' => $segment]) }}" method="POST"
onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan