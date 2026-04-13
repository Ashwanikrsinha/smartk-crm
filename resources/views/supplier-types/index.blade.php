@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Supplier Types</h5>
  <a href="{{ route('supplier-types.create') }}" class="btn btn-primary">Supplier Type</a>
</header>

@php
  $columns = ['id', 'name', 'actions'];
@endphp

<x-datatable id="supplier-types" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#supplier-types').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: `{{ route('supplier-types.index') }}`,
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush