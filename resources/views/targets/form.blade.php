@csrf

<div class="row">

    {{-- SP selection (hidden for SP - they only see their own) --}}
    @if (auth()->user()->isSalesManager() || auth()->user()->isAdmin())
        <div class="col-lg-4 mb-3">
            <label class="form-label">Sales Person <span class="text-danger">*</span></label>
            <select name="user_id" class="form-control" required>
                <option value="">Select SP...</option>
                @foreach ($salesPersons as $sp)
                    <option value="{{ $sp->id }}"
                        {{ (isset($target) && $target->user_id == $sp->id) || old('user_id') == $sp->id ? 'selected' : '' }}>
                        {{ $sp->username }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="col-lg-3 mb-3">
        <label class="form-label">Month <span class="text-danger">*</span></label>
        <select name="month" class="form-control" required>
            <option value="">Select month...</option>
            @foreach ($months as $num => $name)
                <option value="{{ $num }}"
                    {{ (isset($target) && $target->month == $num) || old('month') == $num ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-2 mb-3">
        <label class="form-label">Year <span class="text-danger">*</span></label>
        <select name="year" class="form-control" required>
            @foreach ($years as $y)
                <option value="{{ $y }}"
                    {{ (isset($target) && $target->year == $y) || old('year') == $y ? 'selected' : ($y == date('Y') ? 'selected' : '') }}>
                    {{ $y }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-3 mb-3">
        <label class="form-label">Target Amount (₹) <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">₹</span>
            <input type="number" step="0.01" min="1" name="target_amount" class="form-control"
                value="{{ isset($target) ? $target->target_amount : old('target_amount') }}" placeholder="e.g. 500000"
                required>
        </div>
    </div>

</div>

<div class="d-flex gap-2 mt-2 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <i class="feather icon-save me-1"></i>
        {{ isset($target) ? 'Update Target' : 'Set Target' }}
    </button>
    <a href="{{ route('targets.index') }}" class="btn btn-secondary">Cancel</a>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('select').selectize();
        });
    </script>
@endpush
