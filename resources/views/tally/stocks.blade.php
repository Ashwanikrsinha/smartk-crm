@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Godown Stock</h5>
</header>

@php
  $columns = [
    'id', 
    'godown name',
    'item name',
    'stock opening',
    'stock closing',
    'stock inward',
    'stock outward',
    ];
@endphp

<x-datatable id="stocks" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#stocks').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: {
              url : '{{ route('stocks.index') }}',
              beforeSend: function (xhr){ xhr.setRequestHeader('Authorization', '{{ $bearer_token }}'); }
            },
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'godown_name', name: 'godown_name' },
                { data: 'item_name', name: 'item_name' },
                { data: 'stock_opening', name: 'stock_opening' },
                { data: 'stock_closing', name: 'stock_closing' },
                { data: 'stock_inward', name: 'stock_inward' },
                { data: 'stock_outward', name: 'stock_outward' }
            ],
        });
    });   
</script>
@endpush