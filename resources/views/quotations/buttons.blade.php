@can('view', $quotation)
<a href="{{ route('quotations.show', ['quotation' => $quotation]) }}" class="btn btn-sm text-primary"><i class="feather icon-eye"></i></a>
@endcan

@can('update', $quotation)
<a href="{{ route('quotations.edit', ['quotation' => $quotation]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('delete', $quotation)
<form class="d-inline-block" action="{{ route('quotations.destroy', ['quotation' => $quotation]) }}" method="POST"
onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan
