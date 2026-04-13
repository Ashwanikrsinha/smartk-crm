@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Employees</h5>
  @can('create', App\Models\Employee::class)
    <div class="d-flex align-items-center">
      <div class="dropdown shadow-sm me-2">
        <button class="btn bg-white dropdown-toggle" type="button" id="settings-dropdown" data-bs-toggle="dropdown">
          <i class="feather icon-settings"></i>  
        </button>

        <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="settings-dropdown">
            <a href="{{ route('departments.index') }}" class="dropdown-item">Departments</a>
        </div>
      </div>

      <a href="{{ route('employees.create') }}" class="btn btn-primary">Employee</a>
    </div>
  @endcan
</header>

@php
  $columns = ['id', 'name', 'department', 'phone no.', 'email', 'actions'];
@endphp

<x-datatable id="employees" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#employees').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route('employees.index') }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'department.name', name: 'department.name' },
                { data: 'phone_number', name: 'phone_number' },
                { data: 'email', name: 'email' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush