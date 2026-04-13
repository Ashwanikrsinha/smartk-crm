@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Segment</h5>
  @can('create', App\Models\Segment::class)
   <a href="{{ route('segments.create') }}" class="btn btn-primary">Segment</a>
  @endcan
</header>

@php
  $columns = ['id', 'name', 'category', 'actions'];
@endphp

<x-datatable id="segments" :columns="$columns" />


@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#segments').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route('segments.index') }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'category.name', name: 'category.name' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush