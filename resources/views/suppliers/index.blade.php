@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Suppliers</h5>
  
  @can('create', App\Models\Supplier::class) 
  <div class="d-flex align-items-center">
    <div class="dropdown shadow-sm me-2">
      <button class="btn bg-white dropdown-toggle" type="button" id="settings-dropdown" data-bs-toggle="dropdown">
        <i class="feather icon-settings"></i>  
      </button>
  
      <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="settings-dropdown">
          <a href="{{ route('supplier-types.index') }}" class="dropdown-item">Supplier Types</a>
          <a href="{{ route('segments.index') }}" class="dropdown-item">Segments</a>
          <a href="{{ route('designations.index') }}" class="dropdown-item">Designations</a>
      </div>
    </div>
  
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">Supplier</a>
  </div>
  @endcan
</header>

@php
  $columns = ['id', 'name', 'segment', 'phone no', 'state' ,'actions'];
@endphp

<x-datatable id="suppliers" :columns="$columns" />


@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#suppliers').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: `{{ route('suppliers.index') }}`,
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