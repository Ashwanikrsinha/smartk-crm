@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Leaves</h5>
  <a href="{{ route('leaves.create') }}" class="btn btn-primary">Leave</a>
</header>

@php
  $columns = ['id', 'leave no', 'executive name', 'from date', 'to date', 'leave days', 'leave type', 'status', 'actions'];
@endphp

<x-datatable id="leaves" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#leaves').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route("leaves.index") }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'leave_number', name: 'leave_number' },
                { data: 'user.username', name: 'user.username' },
                { data: 'from_date', name: 'from_date' },
                { data: 'to_date', name: 'to_date' },
                { data: 'leave_days', name: 'leave_days', searchable : false },
                { data: 'type', name: 'type' },
                { data: 'status', name: 'status' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush