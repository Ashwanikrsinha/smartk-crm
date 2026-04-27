@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Leave Details</h5>
            <small class="text-muted">{{ $leave->user->username }} — {{ ucfirst($leave->leave_type) }} Leave</small>
        </div>
        <a href="{{ route('leaves.index') }}" class="btn btn-sm btn-secondary">
            <i class="feather icon-arrow-left me-1"></i> Back
        </a>
    </header>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="bg-white rounded shadow-sm p-4">

                <h6 class="fw-bold border-bottom pb-2 mb-3">
                    <i class="feather icon-calendar me-2 text-primary"></i>Leave Information
                </h6>

                <table class="table table-sm table-borderless">
                    <tr>
                        <th class="text-muted" style="width:40%">Employee</th>
                        <td><strong>{{ $leave->user->username }}</strong></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Leave Type</th>
                        <td>{{ ucfirst($leave->leave_type) }} Leave</td>
                    </tr>
                    <tr>
                        <th class="text-muted">From Date</th>
                        <td>{{ $leave->from_date->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">To Date</th>
                        <td>{{ $leave->to_date->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Total Days</th>
                        <td><strong>{{ $leave->days }} day(s)</strong></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status</th>
                        <td>
                            @php $c = ['pending'=>'warning','approved'=>'success','rejected'=>'danger'][$leave->status] ?? 'secondary'; @endphp
                            <span class="badge bg-{{ $c }}">{{ ucfirst($leave->status) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Reason</th>
                        <td>{{ $leave->reason }}</td>
                    </tr>
                    @if ($leave->manager_remarks)
                        <tr>
                            <th class="text-muted">Manager Remarks</th>
                            <td class="text-{{ $leave->status === 'rejected' ? 'danger' : 'success' }}">
                                {{ $leave->manager_remarks }}
                            </td>
                        </tr>
                    @endif
                </table>

                {{-- SM Approve/Reject (only if pending and viewer is SM/Admin) --}}
                @if ($leave->isPending() && (auth()->user()->isSalesManager() || auth()->user()->isAdmin()))
                    <div class="border-top pt-3 mt-2">
                        <h6 class="fw-bold mb-3">Action Required</h6>
                        <form action="{{ route('leaves.update', $leave) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label">Remarks (optional)</label>
                                <textarea name="manager_remarks" class="form-control" rows="2" placeholder="Add remarks for the employee..."></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="status" value="approved" class="btn btn-success"
                                    onclick="return confirm('Approve this leave?')">
                                    <i class="feather icon-check me-1"></i> Approve
                                </button>
                                <button type="submit" name="status" value="rejected" class="btn btn-danger"
                                    onclick="return confirm('Reject this leave?')">
                                    <i class="feather icon-x me-1"></i> Reject
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
