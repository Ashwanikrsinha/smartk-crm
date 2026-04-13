@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Transports</h5>
  <a href="{{ route('transports.create') }}" class="btn btn-primary">Transport</a>
</header>

@php
  $columns = ['id', 'name', 'phone no.', 'GST no.', 'address', 'actions'];
@endphp

<x-datatable id="transports" :columns="$columns" />


@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#transports').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route("transports.index") }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'phone_number', name: 'phone_number' },
                { data: 'gst_number', name: 'gst_number' },
                { data: 'address', name: 'address' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush