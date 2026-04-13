@can('update', $product)
<a href="{{ route('products.edit', ['product' => $product]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('delete', $product)
<form class="d-inline-block" action="{{ route('products.destroy', ['product' => $product]) }}" method="POST"
onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan