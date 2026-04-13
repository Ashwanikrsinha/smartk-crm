@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Permissions</h5>
  @can('create', App\Models\Permission::class)
  <a href="{{ route('permissions.create') }}" class="btn btn-primary">Permission</a>
  @endcan
</header>


@php
  $columns = ['id', 'name', 'actions'];
@endphp

<x-datatable id="permissions" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#permissions').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route('permissions.index') }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush