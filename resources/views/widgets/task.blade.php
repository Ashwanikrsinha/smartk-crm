<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
       <span>
          <i class="feather icon-bell me-1 bg-primary text-white rounded p-1"></i> Tasks
       </span>
       <span class="badge bg-danger rounded-pill">{{ $tasks->count() }}</span>
    </div>
    <div class="card-body px-0 pt-0 table-responsive" style="max-height: 20rem; overflow-y: auto;">
      @if ($tasks->count() > 0)
          <table class="table" style="min-width: 30rem;">
             <thead>
                <tr class="text-uppercase text-muted small">
                   <th></th>
                   <th class="fw-normal">Task</th>
                   <th class="fw-normal">Status</th>
                   <th class="fw-normal">Deadline</th>
                   <th class="fw-normal">Assignee</th>
                   <th></th>
                </tr>
             </thead>
             <tbody>
                @foreach ($tasks as $task)
                    <tr>
                      <td>
                         <a href="{{ route('tasks.show', ['task' => $task]) }}" target="_blank">
                           <i class="feather icon-external-link me-1 text-muted"></i>
                         </a>
                      </td>
                       <td>{{ $task->task_number }}</td>
                       <td>
                          @if (isset($task->completed_at))
                          <span class="badge alert-primary">complete</span>
                          @else
                              <span class="badge alert-danger">pending</span>
                          @endif
                       </td>
                       <td>{{ $task->deadline_time->format('d M - h:i A') }}</td>
                       <td>{{ Str::limit($task->assignee->username, 20) }}</td>
                       <td>
                           <button type="button" class="btn btn-sm" data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           title="{{ $task->title }}">
                              <i class="feather icon-info text-primary"></i>
                         </button>
                       </td>
                    </tr>
                @endforeach
             </tbody>
          </table>
      @endif
    </div>
 </div>

 
@push('scripts')
    
<script>

   $(document).ready(()=>{

        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
        });

   });

</script>

@endpush