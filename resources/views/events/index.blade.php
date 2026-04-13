@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Events</h5>
  @can('create', App\Models\Event::class)
  <a href="{{ route('events.create') }}" class="btn btn-primary">Event</a>
  @endcan
</header>

@php
  $columns = ['id', 'name', 'actions'];
@endphp

<x-datatable id="events" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#events').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route('events.index') }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush