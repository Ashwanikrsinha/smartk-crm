@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Roles</h5>
  @can('create', App\Models\Role::class)
  <a href="{{ route('roles.create') }}" class="btn btn-primary">Role</a>
  @endcan
</header>

@php
  $columns = ['id', 'name', 'actions'];
@endphp

<x-datatable id="roles" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#roles').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route('roles.index') }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush