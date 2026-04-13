@can('view', $invoice)

<a href="{{ route('invoices.show', ['invoice' => $invoice]) }}" 
    class="btn btn-sm text-primary" title="With Price">
    <i class="feather icon-printer"></i>
</a>


@endcan

@can('update', $invoice)
<a href="{{ route('invoices.edit', ['invoice' => $invoice]) }}" class="btn btn-sm text-primary"><i
        class="feather icon-settings"></i></a>
@endcan

@can('delete', $invoice)
<form class="d-inline-block" action="{{ route('invoices.destroy', ['invoice' => $invoice]) }}" method="POST"
    onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan