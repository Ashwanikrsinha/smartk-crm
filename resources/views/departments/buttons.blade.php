@can('update', $department)
<a href="{{ route('departments.edit', ['department' => $department]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan
@can('delete', $department)
<form class="d-inline-block" action="{{ route('departments.destroy', ['department' => $department]) }}" method="POST"
 onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan