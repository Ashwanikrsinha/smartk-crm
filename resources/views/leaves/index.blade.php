@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Leaves</h5>
    @if(auth()->user()->isSalesPerson())
    <a href="{{ route('leaves.create') }}" class="btn btn-primary btn-sm">
        <i class="feather icon-plus me-1"></i> Apply Leave
    </a>
    @endif
</header>

{{-- ═══ LEAVE BALANCE (SP only) ═════════════════════════ --}}
@if(auth()->user()->isSalesPerson() && isset($leaveBalance))
<div class="row g-3 mb-4">
    @foreach($leaveBalance as $type => $bal)
    <div class="col-6 col-lg-3">
        <div class="bg-white rounded shadow-sm p-3 text-center">
            <h6 class="text-muted text-uppercase small mb-2">{{ ucfirst($type) }} Leave</h6>
            <div class="d-flex justify-content-center align-items-end gap-1 mb-1">
                <h4 class="fw-bold mb-0 text-{{ $bal['remaining'] > 2 ? 'success' : 'danger' }}">
                    {{ $bal['remaining'] }}
                </h4>
                <small class="text-muted mb-1">/ {{ $bal['allowed'] }}</small>
            </div>
            <div class="progress" style="height: 5px;">
                @php $pct = $bal['allowed'] > 0 ? round(($bal['used'] / $bal['allowed']) * 100) : 0; @endphp
                <div class="progress-bar bg-{{ $bal['remaining'] > 2 ? 'success' : 'danger' }}"
                     style="width: {{ $pct }}%"></div>
            </div>
            <small class="text-muted">{{ $bal['used'] }} used</small>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ═══ LEAVES TABLE ═══════════════════════════════════════ --}}
<div class="bg-white rounded shadow-sm p-3">
    <x-datatable id="leaves" :columns="['Employee','Type','From','To','Days','Status','Actions']" />
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('table#leaves').DataTable({
        processing:  true,
        serverSide:  true,
        order:       [[0, 'desc']],
        ajax:        '{{ route('leaves.index') }}',
        columns: [
            { data: 'user.username', name: 'user.username' },
            { data: 'leave_type',   name: 'leave_type' },
            { data: 'from_date',   name: 'from_date' },
            { data: 'to_date',     name: 'to_date' },
            { data: 'days',         orderable: false, searchable: false },
            { data: 'status',       name: 'status',   searchable: false },
            { data: 'action',       orderable: false, searchable: false },
        ],
    });
});
</script>
@endpush
