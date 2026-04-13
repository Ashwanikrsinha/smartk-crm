@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Visit Targets</h5>
  @can('create', App\Models\Target::class)
   <a href="{{ route('targets.create') }}" class="btn btn-primary">Targets</a>
  @endcan
</header>

@php
  $columns = ['id', 'executive', 'target', 'start date', 'end date', 'complete', 'actions'];
@endphp

<x-datatable id="targets" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#targets').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route("targets.index") }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'user.username', name: 'user.username' },
                { data: 'target', name: 'target' },
                { data: 'start_date', name: 'start_date' },
                { data: 'end_date', name: 'end_date' },
                { data: 'complete', name: 'complete' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush