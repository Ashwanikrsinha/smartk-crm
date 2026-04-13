@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Units</h5>
  @can('create', App\Models\Unit::class)
   <a href="{{ route('units.create') }}" class="btn btn-primary">Unit</a>
  @endcan
</header>

@php
    $columns = ['id', 'name', 'actions'];
@endphp

<x-datatable id="units" :columns="$columns" />
  
@endsection

@push('scripts')
<script>
  $(document).ready(() => {

        $('table#units').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route('units.index') }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush