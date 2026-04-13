@can('view', $employee)
<a href="{{ route('employees.show', ['employee' => $employee]) }}" class="btn btn-sm text-primary"><i class="feather icon-eye"></i></a>
@endcan

@can('update', $employee)
<a href="{{ route('employees.edit', ['employee' => $employee]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('delete', $employee)
<form class="d-inline-block" action="{{ route('employees.destroy', ['employee' => $employee]) }}" method="POST"
 onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan