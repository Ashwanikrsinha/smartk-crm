@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>{{ ucwords($type) }} Bills</h5>
  <a href="{{ route('bills.create', ['type' => $type ]) }}" class="btn btn-primary">{{ ucwords($type) }} Bill</a>
</header>

@php
  $columns = ['id', 'bill no', 'bill date', 'type', 'customer name', 'total amount', 'status', 'actions'];
@endphp

<x-datatable id="bills" :columns="$columns" />


@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#bills').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route("bills.index") }}?type={{ $type }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'bill_number', name: 'bill_number' },
                { data: 'bill_date', name: 'bill_date' },
                { data: 'type', name: 'type' },
                { data: 'customer.name', name: 'customer.name', sortable: false },
                { data: 'total_amount', name: 'total_amount' },
                { data: 'status', name: 'status', searchable: false },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });
</script>
@endpush
