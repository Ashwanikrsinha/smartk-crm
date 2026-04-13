@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Groups</h5>
  @can('create', App\Models\Group::class)
   <a href="{{ route('groups.create') }}" class="btn btn-primary">Group</a>
  @endcan
</header>

@php
    $columns = ['id', 'name', 'actions'];
@endphp

<x-datatable id="groups" :columns="$columns" />
  
@endsection

@push('scripts')
<script>
  $(document).ready(() => {

        $('table#groups').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route('groups.index') }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush