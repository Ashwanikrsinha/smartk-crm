@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>States</h5>
  @can('create', App\Models\State::class)
  <a href="{{ route('states.create') }}" class="btn btn-primary">State</a>
  @endcan
</header>

@php
    $columns = ['id', 'name', 'actions'];
@endphp

    <x-datatable id="states" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#states').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route('states.index') }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush