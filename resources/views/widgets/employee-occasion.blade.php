<div class="card border-0 shadow-sm bg-white mb-4">
    <header class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
       <span>
          <i class="feather icon-bookmark me-1 bg-primary text-white rounded p-1"></i>
          Employees Occasions
       </span>
       <span class="badge bg-danger rounded-pill">{{ $employee_occassions->count() }}</span>
    </header>
    <div class="card-body px-0 pt-0 table-responsive">
       @if ($employee_occassions->count() > 0)
       <table class="table align-middle" style="min-width: 30rem;">
          <thead>
             <tr class="text-uppercase text-muted small">
                <th class="fw-normal ps-3">Name</th>
                <th class="fw-normal">Occassion</th>
             </tr>
          </thead>
          <tbody>
             @foreach ($employee_occassions as $employee)
                 <tr>
                    <td class="ps-3">
                       <p class="mb-0">{{ $employee->name }}</p>
                       <small class="text-muted">({{ $employee->department->name }})</small>
                   </td>
                    <td>
                      <p class="mb-1">
                         @if(isset($employee->birth_date) && $employee->birth_date->format('m-d') == date('m-d'))  
                         {{ $employee->birth_date->format('d M, Y') }}
                         <span class="badge alert-primary ms-1">Birthday</span>
                         @endif
                      </p>

                       @if(isset($employee->marriage_date) && $employee->marriage_date->format('m-d') == date('m-d'))  
                       {{ $employee->marriage_date->format('d M, Y') }}
                       <small class="badge alert-primary ms-1">Marriage</small>
                      @endif
                   </td>
                 </tr>
             @endforeach
          </tbody>
       </table>
      @endif
    </div>
 </div>