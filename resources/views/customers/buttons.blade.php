@can('view', $customer)
    <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm text-primary" title="View School">
        <i class="feather icon-eye"></i>
    </a>
@endcan

@can('update', $customer)
    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm text-primary" title="Edit School">
        <i class="feather icon-edit-2"></i>
    </a>
@endcan

@can('create', App\Models\Invoice::class)
    <a href="{{ route('invoices.create') }}?customer_id={{ $customer->id }}" class="btn btn-sm text-success"
        title="New PO for this school">
        <i class="feather icon-file-plus"></i>
    </a>
@endcan

@can('delete', $customer)
    <form class="d-inline-block" action="{{ route('customers.destroy', $customer) }}" method="POST"
        onsubmit="return confirm('Delete {{ addslashes($customer->name) }}? This cannot be undone.')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm text-danger" title="Delete School">
            <i class="feather icon-trash-2"></i>
        </button>
    </form>
@endcan
