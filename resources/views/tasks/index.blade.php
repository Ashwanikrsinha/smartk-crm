@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Tasks</h5>
  @can('create', App\Models\Task::class)
   <a href="{{ route('tasks.create') }}" class="btn btn-primary">Task</a>
  @endcan
</header>

@php
  $columns = ['id', 'task no', 'title', 'deadline', 'assignee', 'assignor', 'complete', 'actions'];
@endphp

<x-datatable id="tasks" :columns="$columns" />

@endsection


@push('scripts')
<script>
  $(document).ready(() => {

        $('table#tasks').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '{{ route("tasks.index") }}',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'task_number', name: 'task_number' },
                { data: 'title', name: 'title' },
                { data: 'deadline_time', name: 'deadline_time' },
                { data: 'assignee.username', name: 'assignee.username' },
                { data: 'assignor.username', name: 'assignor.username' },
                { data: 'completed_at', name: 'completed_at' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
@endpush