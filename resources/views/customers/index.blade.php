@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Customers</h5>

  @can('create', App\Models\Customer::class) 
  <div class="d-flex align-items-center">
    <div class="dropdown shadow-sm me-2">
      <button class="btn bg-white dropdown-toggle" type="button" id="settings-dropdown" data-bs-toggle="dropdown">
        <i class="feather icon-settings"></i>  
      </button>
  
      <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="settings-dropdown">
          <a href="{{ route('customer-types.index') }}" class="dropdown-item">Customer Types</a>
          <a href="{{ route('segments.index') }}" class="dropdown-item">Segments</a>
          <a href="{{ route('designations.index') }}" class="dropdown-item">Designations</a>
      </div>
    </div>
  
    <a href="{{ route('customers.create') }}" class="btn btn-primary">Customer</a>
  </div>
  @endcan

</header>

@php
  $columns = ['id', 'name', 'segment', 'phone no', 'state' ,'actions'];
@endphp

<x-datatable id="customers" :columns="$columns" />


@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#customers').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: `{{ route('customers.index') }}`,
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'segment.name', name: 'segment.name', sortable : false },
                { data: 'phone_number', name: 'phone_number' },
                { data: 'state', name: 'state'},
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush