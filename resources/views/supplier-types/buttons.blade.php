@can('update', $type)
<a href="{{ route('supplier-types.edit', ['type' => $type]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('update', $type)
<form class="d-inline-block" action="{{ route('supplier-types.destroy', ['type' => $type]) }}" method="POST"
 onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan