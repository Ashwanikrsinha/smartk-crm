@extends('layouts.dashboard')
@section('content')

    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Add School Opening Balance</h5>
            <small class="text-muted">Enter carry-forward outstanding balance from previous academic session</small>
        </div>
        <a href="{{ route('opening-balances.index') }}" class="btn btn-secondary btn-sm">
            <i class="feather icon-arrow-left me-1"></i> Back
        </a>
    </header>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading fw-bold mb-1"><i class="feather icon-alert-circle me-1"></i> Please fix the following errors:</h6>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm col-lg-8 mx-auto">
        <div class="card-body p-4">
            <form action="{{ route('opening-balances.store') }}" method="POST">
                @csrf

                {{-- School Selection --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">School <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select selectize" required>
                        <option value="">Select School</option>
                        @foreach ($schools as $sch)
                            <option value="{{ $sch->id }}" {{ old('customer_id') == $sch->id ? 'selected' : '' }}>
                                [{{ $sch->school_code }}] {{ $sch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Session Year & Sales Person --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Academic Session <span class="text-danger">*</span></label>
                        <select name="session_year" class="form-select" required>
                            @for ($y = date('Y') + 1; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ old('session_year', $currentSessionYear) == $y ? 'selected' : '' }}>
                                    Session {{ $y }}-{{ str_pad(($y + 1) % 100, 2, '0', STR_PAD_LEFT) }}
                                </option>
                            @endfor
                        </select>
                        <small class="text-muted">The session in which this carry-forward amount should appear.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Sales Person (Optional)</label>
                        <select name="user_id" class="form-select selectize">
                            <option value="">Select Sales Person</option>
                            @foreach ($spUsers as $sp)
                                <option value="{{ $sp->id }}" {{ old('user_id') == $sp->id ? 'selected' : '' }}>
                                    {{ $sp->username }} ({{ $sp->emp_code ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Sales person who generated the original sales.</small>
                    </div>
                </div>

                {{-- Amounts --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Opening Balance / Carry-Forward Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="opening_amount"
                            value="{{ old('opening_amount') }}" class="form-control" placeholder="e.g. 300000 or -50000" required>
                        <small class="text-muted">Enter positive for pending dues, or negative for advance / credit balance.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Already Collected (₹)</label>
                        <input type="number" step="0.01" name="opening_collected"
                            value="{{ old('opening_collected', 0) }}" class="form-control" placeholder="0 if not collected">
                        <small class="text-muted">Enter 0 if no payment has been made against this balance yet.</small>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Notes / Remarks</label>
                    <textarea name="notes" rows="3" class="form-control" placeholder="e.g. Carry forward from 2024-25 session, PO #123 balance">{{ old('notes') }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('opening-balances.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="feather icon-check me-1"></i> Save Opening Balance
                    </button>
                </div>

            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.selectize').selectize();
        });
    </script>
@endpush
