@can('view', $user)
<a href="{{ route('users.show', ['user' => $user]) }}" class="btn btn-sm text-primary"><i class="feather icon-eye"></i></a>
@endcan

@can('update', $user)
<a href="{{ route('users.edit', ['user' => $user]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('delete', $user)
<form class="d-inline-block" action="{{ route('users.destroy', ['user' => $user]) }}" method="POST"
 onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan