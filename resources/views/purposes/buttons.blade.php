@can('update', $purpose)
<a href="{{ route('purposes.edit', ['purpose' => $purpose]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan
@can('delete', $purpose)
<form class="d-inline-block" action="{{ route('purposes.destroy', ['purpose' => $purpose]) }}" method="POST"
 onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan