@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Visits</h5>

  @can('create', App\Models\Visit::class)


    <div class="d-flex align-items-center">

      <div class="d-flex align-items-center">

        <div class="dropdown shadow-sm me-2">
        <button class="btn bg-white dropdown-toggle" type="button" id="settings-dropdown" data-bs-toggle="dropdown">
          <i class="feather icon-settings"></i>
        </button>

        <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="settings-dropdown">
            <a href="{{ route('purposes.index') }}" class="dropdown-item">Purposes</a>
        </div>
      </div>


      <div class="d-flex align-items-center">

        <div class="input-group me-2">
          <span class="input-group-text bg-white">
            <i class="feather icon-calendar"></i>
          </span>
          <input type="date" class="form-control" id="visit-date">
        </div>

        <a href="{{ route('visits.create') }}" class="btn btn-primary">Visit</a>

      </div>

    </div>

  @endcan
</header>

@include('visits.accordion')

@php
  $columns = ['id', 'visit no', 'visit date', 'username', 'customer', 'purpose', 'level', 'status', 'actions'];
@endphp

<x-datatable id="visits" :columns="$columns" :small="true" />


@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('input[type=date]').flatpickr({
            altInput: true,
            altFormat: 'F j, Y',
            dateFormat: 'Y-m-d',
        });

      let table =  $('table#visits').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route("visits.index") }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'visit_number', name: 'visit_number' },
                { data: 'visit_date', name: 'visit_date' },
                { data: 'user_name_emp_code', name: 'user_name_emp_code', sortable : false },
                { data: 'customer.name', name: 'customer.name', sortable : false },
                { data: 'purpose.name', name: 'purpose.name', sortable : false },
                { data: 'level', name: 'level', sortable : false },
                { data: 'status', name: 'status', sortable : false },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });

        $('#visit-date').change(function(){
          table.column(2).search($(this).val()).draw();
      });

    });

</script>
@endpush
