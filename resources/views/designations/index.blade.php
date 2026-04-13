@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Designations</h5>
  @can('create', App\Models\Designation::class)
  <a href="{{ route('designations.create') }}" class="btn btn-primary">Designation</a>
  @endcan
</header>

@php
  $columns = ['id', 'name', 'actions'];
@endphp

<x-datatable id="designations" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#designations').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route('designations.index') }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush