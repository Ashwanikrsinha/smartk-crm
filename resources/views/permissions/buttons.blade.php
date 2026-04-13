@can('update', $permission)
<a href="{{ route('permissions.edit', ['permission' => $permission]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan
@can('delete', $permission)
<form class="d-inline-block" action="{{ route('permissions.destroy', ['permission' => $permission]) }}" method="POST"
 onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan