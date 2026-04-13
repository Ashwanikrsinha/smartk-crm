@can('view', $bill)

<a href="{{ route('bills.show', ['bill' => $bill, 'type' => $bill::WITH_PRICE]) }}" 
    class="btn btn-sm text-primary" title="With Price">
    <i class="feather icon-printer"></i>
</a>

<a href="{{ route('bills.show', ['bill' => $bill, 'type' => $bill::WITHOUT_PRICE ]) }}" 
    class="btn btn-sm text-primary" title="Without Price">
    <i class="feather icon-printer"></i>
</a>

@endcan

@can('update', $bill)
<a href="{{ route('bills.edit', ['bill' => $bill]) }}" class="btn btn-sm text-primary"><i
        class="feather icon-settings"></i></a>
@endcan

@can('delete', $bill)
<form class="d-inline-block" action="{{ route('bills.destroy', ['bill' => $bill]) }}" method="POST"
    onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan