@can('view', $supplier)
<a href="{{ route('suppliers.show', ['supplier' => $supplier]) }}" class="btn btn-sm text-primary"><i class="feather icon-eye"></i></a>
@endcan

@can('update', $supplier)
<a href="{{ route('suppliers.edit', ['supplier' => $supplier]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('delete', $supplier)
<form class="d-inline-block" action="{{ route('suppliers.destroy', ['supplier' => $supplier]) }}" method="POST"
onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan
