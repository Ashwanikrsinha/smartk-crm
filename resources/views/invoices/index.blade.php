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
        $columns = array_values(
            array_filter([
                'PO Number',
                'Date',
                'School',
                'Sales Person',
                !$isWarehouse ? 'PO Amount' : null,
                !$isWarehouse && !$isMarketing ? 'Billed' : null,
                !$isWarehouse && !$isMarketing ? 'Collected' : null,
                !$isWarehouse && !$isMarketing ? 'Outstanding' : null,
                'Status',
                'Actions',
            ]),
        );
    @endphp

    <div class="bg-white rounded shadow-sm p-3">
        <x-datatable id="invoices" :columns="$columns" />
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            const isWarehouse = {{ auth()->user()->isWarehouse() ? 'true' : 'false' }};
            const isMarketing = {{ auth()->user()->isMarketing() ? 'true' : 'false' }};

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
                    orderable: false
                },
                {
                    data: 'user_name_emp_code',
                    name: 'user_name_emp_code',
                    orderable: false
                },
            ];

            // Both Marketing and non-Warehouse see PO Amount + Billed
            if (!isWarehouse) {
                columns.push({
                    data: 'amount',
                    name: 'amount',
                    searchable: false
                });
            }

            // Only non-Warehouse AND non-Marketing see Collected + Outstanding
            if (!isWarehouse && !isMarketing) {
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
