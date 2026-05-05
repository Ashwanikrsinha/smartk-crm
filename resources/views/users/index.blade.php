@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Users</h5>
  @can('create', App\Models\User::class)
   <a href="{{ route('users.create') }}" class="btn btn-primary">User</a>
  @endcan
</header>

@php
  $columns = ['id','Employee Code', 'username', 'email', 'department', 'role', 'status', 'actions'];
@endphp

<x-datatable id="users" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#users').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route('users.index') }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'emp_code', name: 'emp_code' },
                { data: 'username', name: 'username' },
                { data: 'email', name: 'email' },
                { data: 'department.name', name: 'department.name' },
                { data: 'role.name', name: 'role.name' },
                { data: 'status', name: 'status', searchable: false },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });
</script>
@endpush
