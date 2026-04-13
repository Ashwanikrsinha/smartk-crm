@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Customer Types</h5>
  <a href="{{ route('customer-types.create') }}" class="btn btn-primary">Customer Type</a>
</header>

@php
  $columns = ['id', 'name', 'actions'];
@endphp

<x-datatable id="customer-types" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#customer-types').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: `{{ route('customer-types.index') }}`,
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush