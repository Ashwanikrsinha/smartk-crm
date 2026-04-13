@can('view', $customer)
<a href="{{ route('customers.show', ['customer' => $customer]) }}" class="btn btn-sm text-primary"><i class="feather icon-eye"></i></a>
@endcan

@can('update', $customer)
<a href="{{ route('customers.edit', ['customer' => $customer]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('delete', $customer)
<form class="d-inline-block" action="{{ route('customers.destroy', ['customer' => $customer]) }}" method="POST"
onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan
