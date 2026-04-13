<div class="card border-0 shadow-sm bg-white mb-4">
    <header class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
       <span>
          <i class="feather icon-bookmark me-1 bg-primary text-white rounded p-1"></i>
          Staff On Leave - Today
       </span>
       <span class="badge bg-danger rounded-pill">{{ $leaves->count() }}</span>
    </header>
    <div class="card-body px-0 pt-0 table-responsive" style="max-height: 20rem; overflow-y: auto;">
       @if ($leaves->count() > 0)
       <table class="table" style="min-width: 30rem;">
          <thead>
             <tr class="text-uppercase text-muted small">
                <th></th>
                <th class="fw-normal">Leave</th>
                <th class="fw-normal">From - To</th>
                <th class="fw-normal">Status</th>
                <th class="fw-normal">Executive</th>
             </tr>
          </thead>
          <tbody>
             @foreach ($leaves as $leave)
                 <tr>
                   <td>
                      <a href="{{ route('leaves.show', ['leave' => $leave]) }}" target="_blank">
                        <i class="feather icon-external-link me-1 text-muted"></i>
                      </a>
                   </td>
                    <td>{{ $leave->leave_number }}</td>
                    <td>{{$leave->from_date->format('d M') }} - {{$leave->to_date->format('d M') }}</td>
                    <td>
                       <span class="badge alert-primary">{{ $leave->status }}</span>
                    </td>
                    <td>{{ Str::limit($leave->user->username, 20) }}</td>
                 </tr>
             @endforeach
          </tbody>
       </table>
      @endif
    </div>
 </div>