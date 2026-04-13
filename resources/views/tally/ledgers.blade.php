@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Ledgers</h5>
</header>

@php
  $columns = [
    'id', 
    'company name', 
    'ledger name', 
    'ledger id', 
    'master id',  
    'group', 
    'state', 
    'gst type'
    ];
@endphp

<x-datatable id="ledgers" :columns="$columns" :small="true" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#ledgers').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: {
              url : '{{ route('ledgers.index') }}',
              beforeSend: function (xhr){ xhr.setRequestHeader('Authorization', '{{ $bearer_token }}'); }
            },
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'company_name', name: 'company_name' },
                { data: 'ledger_name', name: 'ledger_name' },
                { data: 'ledger_id', name: 'ledger_id' },
                { data: 'master_id', name: 'master_id' },
                { data: 'ledger_group', name: 'ledger_group' },
                { data: 'state', name: 'state' },
                { data: 'gst_type', name: 'gst_type' },
            ],
        });
    });   
</script>
@endpush