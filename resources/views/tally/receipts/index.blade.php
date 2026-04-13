@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Receipts</h5>
</header>

@php
  $columns = [
    'id', 
    'voucher no.', 
    'voucher date', 
    'voucher type', 
    'reference no.',  
    'company name',
    'amount', 
    'receipt mode',
    'cheque no.',
    'actions'
    ];
@endphp

<x-datatable id="receipts" :columns="$columns" :small="true" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#receipts').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: {
              url : '{{ route('receipts.index') }}',
              beforeSend: function (xhr){ xhr.setRequestHeader('Authorization', '{{ $bearer_token }}'); }
            },
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'voucher_number', name: 'voucher_number' },
                { data: 'voucher_date', name: 'voucher_date' },
                { data: 'voucher_type', name: 'voucher_type' },
                { data: 'reference_number', name: 'reference_number' },
                { data: 'company_name', name: 'company_name' },
                { data: 'amount', name: 'amount' },
                { data: 'payment_mode', name: 'payment_mode' },
                { data: 'cheque_number', name: 'cheque_number' },
                { data: 'actions', name: 'actions', searchable: false, orderable: false },
            ],
        });
    });   
</script>
@endpush