@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Items</h5>
</header>

@php
  $columns = [
    'id', 
    'company name', 
    'item name', 
    'item id', 
    'master id',
    'unit', 
    'part number', 
    'group', 
    'item alias',
    'category', 
    'hsn code',
    'tax rate'
    ];
@endphp

<x-datatable id="items" :columns="$columns" :small="true" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#items').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: {
              url : '{{ route('items.index') }}',
              beforeSend: function (xhr){ xhr.setRequestHeader('Authorization', '{{ $bearer_token }}'); }
            },
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'company_name', name: 'company_name' },
                { data: 'name', name: 'name' },
                { data: 'item_id', name: 'item_id' },
                { data: 'master_id', name: 'master_id' },
                { data: 'unit', name: 'unit' },
                { data: 'part_number', name: 'part_number' },
                { data: 'group', name: 'group' },
                { data: 'item_alias', name: 'item_alias' },
                { data: 'category', name: 'category' },
                { data: 'hsn_code', name: 'hsn_code' },
                { data: 'tax_rate', name: 'tax_rate' },
            ],
        });
    });   
</script>
@endpush