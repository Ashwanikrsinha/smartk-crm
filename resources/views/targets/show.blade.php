@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5>Target Details</h5>
            <small class="text-muted">{{ $target->user->username }} — {{ $target->month_label }}</small>
        </div>
        <a href="{{ route('targets.index') }}" class="btn btn-sm btn-secondary">Back</a>
    </header>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-3 text-center">
                <h6 class="text-muted small mb-1">Target Amount</h6>
                <h4 class="fw-bold text-primary mb-0">₹{{ number_format($target->target_amount, 2) }}</h4>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-3 text-center">
                <h6 class="text-muted small mb-1">Achieved</h6>
                <h4 class="fw-bold text-success mb-0">₹{{ number_format($achieved, 2) }}</h4>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-3 text-center">
                <h6 class="text-muted small mb-1">Remaining</h6>
                <h4 class="fw-bold text-{{ $pct >= 100 ? 'success' : 'danger' }} mb-0">
                    ₹{{ number_format(max($target->target_amount - $achieved, 0), 2) }}
                </h4>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="bg-white rounded shadow-sm p-3 text-center">
                <h6 class="text-muted small mb-1">Achievement</h6>
                <h4 class="fw-bold text-{{ $pct >= 100 ? 'success' : ($pct >= 60 ? 'warning' : 'danger') }} mb-0">
                    {{ $pct }}%
                </h4>
            </div>
        </div>
    </div>

    <div class="bg-white rounded shadow-sm p-4">
        <h6 class="fw-bold border-bottom pb-2 mb-3">Progress</h6>
        <div class="progress mb-2" style="height: 24px;">
            @php $color = $pct >= 100 ? 'success' : ($pct >= 60 ? 'warning' : 'danger'); @endphp
            <div class="progress-bar bg-{{ $color }} fw-bold" style="width: {{ min($pct, 100) }}%">
                {{ $pct }}%
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <small class="text-muted">₹0</small>
            <small class="text-muted">₹{{ number_format($target->target_amount, 2) }}</small>
        </div>
    </div>
@endsection
