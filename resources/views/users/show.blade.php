@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>User</h5>
  <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">Back</a>
</header>

<div class="card border-0 shadow-sm mb-4">
  <header class="card-header bg-white py-3">
      <i class="feather icon-user me-1 bg-primary text-white rounded p-1"></i>
  </header>
<div class="card-body px-0 pt-0">
  <section class="table-responsive rounded">
    <table class="table" style="min-width: 40rem;">
      <tbody>
        <tr>
          <th class="ps-3">Username</th>
          <td>{{ $user->username }}</td>
        </tr>
        <tr>
          <th class="ps-3">Email Address</th>
          <td>{{ $user->email }}</td>
        </tr>
        <tr>
          <th class="ps-3">Role</th>
          <td>{{ $user->role->name }}</td>
        </tr>
        <tr>
          <th class="ps-3">Department</th>
          <td>{{ $user->department->name }}</td>
        </tr>
        <tr>
          <th class="ps-3">Reportive To</th>
          <td>{{ isset($user->reportiveTo) ? $user->reportiveTo->username : '' }}</td>
        </tr>
        <tr>
          <th class="ps-3">Status</th>
          <td>
            @if ($user->is_disable)
              Disabled <i class="feather icon-x-circle text-danger ms-1"></i>
              - 
              <span>Inactive Date : {{ $user->inactive_date->format('d M, Y') }}</span>
            @else
             Active <i class="feather icon-check-circle text-success ms-1"></i>
            @endif
          </td>
        </tr>
      </tbody>
    </table>
  </section>
</div>
</div>

  @if ($logs->count() > 0 && auth()->user()->hasPermission('browse_logs'))
  <section class="table-responsive rounded shadow-sm rounded bg-white mb-4">
    <table class="table" id="logs">
      <thead>
        <tr>
          <th colspan="3" class="ps-3 py-3 fw-normal">
            <i class="feather icon-user me-1 bg-primary text-white rounded p-1"></i>
            Logs
          </th>
        </tr>
        <tr>
          <th class="ps-3">IP Address</th>
          <th class="ps-3">Login Time</th>
          <th class="ps-3">Logout Time</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($logs as $log)
        <tr>
          <td class="ps-3">{{ $log->ip_address }}</td>
          <td>{{ $log->login_time->format('d M, Y - h:i:s A') }}</td>
          <td>{{ isset($log->logout_time) ? $log->logout_time->format('d M, Y - h:i:s A') : '' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    {{ $logs->links() }}
  </section>
  @endif

</div>

@endsection


@push('scripts')

<script>
    $(document).ready(() => {
      
      $('#qualification table').each(function(){ $(this).addClass('table table-bordered w-100')});

    });
</script>

@endpush