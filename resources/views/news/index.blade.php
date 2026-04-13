@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>News</h5>

  @can('create', App\Models\News::class)
    <div class="d-flex align-items-center">

      <div class="dropdown shadow-sm me-2">
        <button class="btn bg-white dropdown-toggle" type="button" id="settings-dropdown" data-bs-toggle="dropdown">
          <i class="feather icon-settings"></i>  
        </button>

        <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="settings-dropdown">
            <a href="{{ route('events.index') }}" class="dropdown-item">Event Types</a>
        </div>
      </div>

      <a href="{{ route('news.create') }}" class="btn btn-primary">News</a>

    </div>
  @endcan

</header>

@php
  $columns = ['id', 'title', 'event', 'status', 'publish date', 'actions'];
@endphp

<x-datatable id="news" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#news').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route("news.index") }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'title', name: 'title' },
                { data: 'event.name', name: 'event.name' },
                { data: 'is_active', name: 'is_active' },
                { data: 'published_at', name: 'published_at' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush