@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Purchases</h5>
</header>

@php
  $columns = [
    'id', 
    'voucher no.', 
    'voucher date', 
    'type', 
    'party name',  
    'ledger name',
    'entered by', 
    'actions'
    ];
@endphp

<x-datatable id="purchases" :columns="$columns" :small="true" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#purchases').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: {
              url : '{{ route('purchases.index') }}',
              beforeSend: function (xhr){ xhr.setRequestHeader('Authorization', '{{ $bearer_token }}'); }
            },
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'voucher_number', name: 'voucher_number' },
                { data: 'voucher_date', name: 'voucher_date' },
                { data: 'voucher_type', name: 'voucher_type' },
                { data: 'party_name', name: 'party_name' },
                { data: 'ledger_name', name: 'ledger_name' },
                { data: 'entered_by', name: 'entered_by' },
                { data: 'actions', name: 'actions', searchable: false, orderable: false },
            ],
        });
    });   
</script>
@endpush