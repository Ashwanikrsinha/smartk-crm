@can('update', $role)
<a href="{{ route('roles.edit', ['role' => $role]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan
@can('delete', $role)
<form class="d-inline-block" action="{{ route('roles.destroy', ['role' => $role]) }}" method="POST"
 onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan