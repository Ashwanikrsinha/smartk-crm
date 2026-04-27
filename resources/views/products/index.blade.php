@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Products</h5>

  @can('create', App\Models\Product::class)
    <div class="d-flex align-items-center">

      <div class="dropdown shadow-sm me-2">
        <button class="btn bg-white dropdown-toggle" type="button" id="settings-dropdown" data-bs-toggle="dropdown">
          <i class="feather icon-settings"></i>
        </button>

        <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="settings-dropdown">
            <a href="{{ route('units.index') }}" class="dropdown-item">Units</a>
            {{-- <a href="{{ route('groups.index') }}" class="dropdown-item">Groups</a> --}}
            <a href="{{ route('categories.index') }}" class="dropdown-item">Categories</a>
        </div>
      </div>

      <a href="{{ route('products.create') }}" class="btn btn-primary">Product</a>

    </div>
  @endcan

</header>

@php
  $columns = ['id', 'name', 'HSN code', 'unit',  'category',  're order level', 'actions'];
@endphp

<x-datatable id="products" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#products').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route('products.index') }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'code', name: 'code' },
                { data: 'unit.name', name: 'unit.name' },
                // { data: 'group.name', name: 'group.name' },
                { data: 'category.name', name: 'category.name' },
                { data: 'reorder_level', name: 'reorder_level' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });
</script>
@endpush
