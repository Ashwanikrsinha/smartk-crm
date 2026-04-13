@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Purposes</h5>
  @can('create', App\Models\Purpose::class)
  <a href="{{ route('purposes.create') }}" class="btn btn-primary">Purpose</a>
  @endcan
</header>

@php
  $columns = ['id', 'name', 'actions'];
@endphp

<x-datatable id="purposes" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#purposes').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route('purposes.index') }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush