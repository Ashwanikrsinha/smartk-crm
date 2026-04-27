@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Performa Invoices</h5>
  <a href="{{ route('invoices.create') }}" class="btn btn-primary">Invoice</a>
</header>

@php
  $columns = ['id', 'invoice no', 'invoice date', 'visit no', 'customer', 'amount', 'status', 'actions'];
@endphp

<x-datatable id="invoices" :columns="$columns" />


@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#invoices').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route("invoices.index") }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'invoice_number', name: 'invoice_number' },
                { data: 'invoice_date', name: 'invoice_date' },
                { data: 'visit.visit_number', name: 'visit.visit_number' },
                { data: 'customer.name', name: 'customer.name' },
                { data: 'amount', name: 'amount' },
                { data: 'status', name: 'status', searchable: false },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush