@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Purchase Orders</h5>
            <small class="text-muted">Manage all sales orders</small>
        </div>
        @can('create', App\Models\Invoice::class)
            <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
                <i class="feather icon-plus me-1"></i> New PO
            </a>
        @endcan
    </header>

    @php
        $isWarehouse = auth()->user()->role?->name === 'Warehouse';
        $columns = [
            'po_number',
            'date',
            'school',
            'sales_person',
            $isWarehouse ? '—' : 'PO Amount',
            $isWarehouse ? '—' : 'Billed',
            $isWarehouse ? '—' : 'Collected',
            $isWarehouse ? '—' : 'Outstanding',
            'status',
            'actions',
        ];
    @endphp

    <div class="bg-white rounded shadow-sm p-3">
        <x-datatable id="invoices" :columns="$columns" />
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $('table#invoices').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, 'desc']
                ],
                ajax: '{{ route('invoices.index') }}',
                columns: [{
                        data: 'po_number',
                        name: 'po_number'
                    },
                    {
                        data: 'invoice_date',
                        name: 'invoice_date'
                    },
                    {
                        data: 'customer.name',
                        name: 'customer.name',
                        sortable: false
                    },
                    {
                        data: 'user.username',
                        name: 'user.username',
                        sortable: false
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        searchable: false
                    },
                    {
                        data: 'billing_amount',
                        name: 'billing_amount',
                        searchable: false
                    },
                    {
                        data: 'collected_amount',
                        name: 'collected_amount',
                        searchable: false
                    },
                    {
                        data: 'outstanding_amount',
                        name: 'outstanding_amount',
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        searchable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });

        });
    </script>
@endpush
