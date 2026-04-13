@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Categories</h5>
  @can('create', App\Models\Category::class)
   <a href="{{ route('categories.create') }}" class="btn btn-primary">Category</a>
  @endcan
</header>

@php
    $columns = ['id', 'name', 'actions'];
@endphp

<x-datatable id="categories" :columns="$columns" />
  
@endsection

@push('scripts')
<script>
  $(document).ready(() => {

        $('table#categories').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route('categories.index') }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush