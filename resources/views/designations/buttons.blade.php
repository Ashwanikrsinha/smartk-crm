@can('update', $designation)
<a href="{{ route('designations.edit', ['designation' => $designation]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan
@can('delete', $designation)
<form class="d-inline-block" action="{{ route('designations.destroy', ['designation' => $designation]) }}" method="POST"
 onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan