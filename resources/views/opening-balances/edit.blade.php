@extends('layouts.dashboard')
@section('content')

    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Edit School Opening Balance</h5>
            <small class="text-muted">{{ $openingBalance->customer->name ?? '' }} ({{ $openingBalance->customer->school_code ?? '' }})</small>
        </div>
        <a href="{{ route('opening-balances.index', ['session_year' => $openingBalance->session_year]) }}" class="btn btn-secondary btn-sm">
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
            <form action="{{ route('opening-balances.update', $openingBalance) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- School & Session (Read-only on edit) --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">School</label>
                        <input type="text" class="form-control" value="[{{ $openingBalance->customer->school_code ?? '' }}] {{ $openingBalance->customer->name ?? '' }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Academic Session</label>
                        <input type="text" class="form-control" value="Session {{ $openingBalance->sessionLabel() }}" readonly>
                    </div>
                </div>

                {{-- Sales Person --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Sales Person</label>
                    <select name="user_id" class="form-select selectize">
                        <option value="">Select Sales Person</option>
                        @foreach ($spUsers as $sp)
                            <option value="{{ $sp->id }}" {{ old('user_id', $openingBalance->user_id) == $sp->id ? 'selected' : '' }}>
                                {{ $sp->username }} ({{ $sp->emp_code ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Amount and Collections summary --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Opening Balance Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="opening_amount"
                            value="{{ old('opening_amount', $openingBalance->opening_amount) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Total Collected So Far (₹)</label>
                        <input type="text" class="form-control text-success fw-bold"
                            value="₹{{ number_format($openingBalance->opening_collected, 2) }}" readonly>
                        <small class="text-muted">Managed via recorded payment entries.</small>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Notes / Remarks</label>
                    <textarea name="notes" rows="3" class="form-control">{{ old('notes', $openingBalance->notes) }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('opening-balances.index', ['session_year' => $openingBalance->session_year]) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="feather icon-check me-1"></i> Update Opening Balance
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- Collections history if any --}}
    @if ($openingBalance->collections->count() > 0)
        <div class="card border-0 shadow-sm col-lg-8 mx-auto mt-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="feather icon-clock me-2 text-primary"></i> Payment History</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Mode</th>
                                <th>Ref #</th>
                                <th>Recorded By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($openingBalance->collections as $col)
                                <tr>
                                    <td>{{ $col->collected_at ? $col->collected_at->format('d M, Y') : '-' }}</td>
                                    <td class="text-success fw-bold">₹{{ number_format($col->collected_amount, 2) }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ strtoupper($col->payment_mode) }}</span></td>
                                    <td>{{ $col->reference_number ?? '—' }}</td>
                                    <td>{{ $col->collectedBy->username ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('opening-balance-collections.destroy', $col) }}" method="POST"
                                            onsubmit="return confirm('Delete this payment entry?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger">
                                                <i class="feather icon-trash-2"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.selectize').selectize();
        });
    </script>
@endpush
