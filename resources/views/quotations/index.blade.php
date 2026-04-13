@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Quotations</h5>
  <a href="{{ route('quotations.create') }}" class="btn btn-primary">Quotation</a>
</header>

@php
  $columns = ['id', 'quotation no', 'quotation date', 'visit no', 'customer', 'username', 'status', 'actions'];
@endphp

<x-datatable id="quotations" :columns="$columns" />


@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#quotations').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route("quotations.index") }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'quotation_number', name: 'quotation_number' },
                { data: 'quotation_date', name: 'quotation_date' },
                { data: 'visit.visit_number', name: 'visit.visit_number' },
                { data: 'customer.name', name: 'customer.name' },
                { data: 'user.username', name: 'user.username' },
                { data: 'status', name: 'status' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush