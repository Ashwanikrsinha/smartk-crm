@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Department</h5>
  @can('create', App\Models\Department::class)
  <a href="{{ route('departments.create') }}" class="btn btn-primary">Department</a>
  @endcan
</header>

@php
  $columns = ['id', 'name', 'actions'];
@endphp

<x-datatable id="departments" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#departments').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route('departments.index') }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush