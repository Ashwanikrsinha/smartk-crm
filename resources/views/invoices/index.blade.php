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
        $isWarehouse = auth()->user()->isWarehouse();
        $isMarketing = auth()->user()->isMarketing();
        // Both Warehouse and Marketing cannot see financial columns
        $hideFinancials = $isWarehouse || $isMarketing;

        $columns = [
            'PO Number',
            'Date',
            'School',
            'Sales Person',
            $hideFinancials ? null : 'PO Amount',
            $hideFinancials ? null : 'Billed',
            $hideFinancials ? null : 'Collected',
            $hideFinancials ? null : 'Outstanding',
            'Status',
            'Actions',
        ];
        // Remove null entries cleanly
        $columns = array_values(array_filter($columns));
    @endphp

    <div class="bg-white rounded shadow-sm p-3">
        <x-datatable id="invoices" :columns="$columns" />
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            const hideFinancials =
                {{ auth()->user()->isMarketing() || auth()->user()->isWarehouse() ? 'true' : 'false' }};

            // Build columns array conditionally
            const columns = [{
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
                    data: 'user_name_emp_code',
                    name: 'user_name_emp_code',
                    sortable: false
                },
            ];

            if (!hideFinancials) {
                columns.push({
                    data: 'amount',
                    name: 'amount',
                    searchable: false
                });
                columns.push({
                    data: 'billing_amount',
                    name: 'billing_amount',
                    searchable: false
                });
                columns.push({
                    data: 'collected_amount',
                    name: 'collected_amount',
                    searchable: false
                });
                columns.push({
                    data: 'outstanding_amount',
                    name: 'outstanding_amount',
                    searchable: false
                });
            }

            columns.push({
                data: 'status',
                name: 'status',
                searchable: false
            });
            columns.push({
                data: 'action',
                orderable: false,
                searchable: false
            });

            $('table#invoices').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, 'desc']
                ],
                ajax: '{{ route('invoices.index') }}',
                columns: columns,
            });

        });
    </script>
@endpush
