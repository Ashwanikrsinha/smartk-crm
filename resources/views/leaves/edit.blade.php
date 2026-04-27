@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h5>Edit Leave</h5>
        <a href="{{ route('leaves.index') }}" class="btn btn-sm btn-secondary">Back</a>
    </header>

    <form action="{{ route('leaves.update', $leave) }}" method="POST" class="bg-white rounded shadow-sm p-4"
        style="max-width: 600px;">
        @csrf
        @method('PUT')

        <div class="row">

            <div class="col-lg-6 mb-3">
                <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                <select name="leave_type" class="form-control" required>
                    @foreach ($leaveTypes as $type)
                        <option value="{{ $type }}" {{ $leave->leave_type === $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }} Leave
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-3 mb-3">
                <label class="form-label">From Date <span class="text-danger">*</span></label>
                <input type="date" name="start_date" class="form-control"
                    value="{{ $leave->start_date->format('Y-m-d') }}" required>
            </div>

            <div class="col-lg-3 mb-3">
                <label class="form-label">To Date <span class="text-danger">*</span></label>
                <input type="date" name="end_date" class="form-control" value="{{ $leave->end_date->format('Y-m-d') }}"
                    required>
            </div>

            <div class="col-12 mb-3">
                <label class="form-label">Reason <span class="text-danger">*</span></label>
                <textarea name="reason" class="form-control" rows="3" required minlength="10">{{ $leave->reason }}</textarea>
            </div>

        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="feather icon-save me-1"></i> Update Leave
            </button>
            <a href="{{ route('leaves.index') }}" class="btn btn-secondary">Cancel</a>
        </div>

    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('select').selectize();
        });
    </script>
@endpush
