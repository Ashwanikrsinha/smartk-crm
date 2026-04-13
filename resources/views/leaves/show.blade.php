@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5 class="d-inline-block me-2 mb-0">Leaves</h5>
    <a href="{{ route('leaves.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<div class="card border-0 shadow-sm">
    <header class="card-header bg-white py-3">
        <i class="feather icon-bookmark me-1 bg-primary text-white rounded p-1"></i>
        Leave No. {{ $leave->leave_number }}
    </header>
  <div class="card-body p-0">
    <section class="table-responsive mb-4">
        <table class="table" style="min-width: 40rem;">
            <tbody>
                <tr>
                    <th class="ps-3">Executive Name</th>
                    <td>
                        {{ $leave->user->username }}
                    </td>
                </tr>
                <tr>
                    <th class="ps-3">From Date - To Date</th>
                    <td>{{$leave->from_date->format('d M, Y') }} - {{$leave->to_date->format('d M, Y') }}</td>
                </tr> 
                <tr>
                    <th class="ps-3">Leave Days</th>
                    <td>
                        {{ $leave->to_date->diffInDays($leave->from_date) + 1 }}
                    </td>
                </tr> 
                <tr>
                    <th class="ps-3">Leave Type</th>
                    <td>{{ ucwords($leave->type) }}</td>
                </tr> 
                <tr>
                    <th class="ps-3">Status</th>
                    <td>
                        {{ ucwords($leave->status) }}
                    </td>
                </tr>
                <tr>
                    <th class="ps-3">Comment</th>
                    <td>{{ $leave->comment }}</td>
                </tr>
            </tbody>
        </table>
    </section>
  </div>
</div>

@endsection
