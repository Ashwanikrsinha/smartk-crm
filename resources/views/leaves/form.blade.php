@csrf
<div class="row">
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Executive Name</label>
        <div class="form-control">
            {{ $mode == 'edit' ? $leave->user->username : auth()->user()->username  }}
        </div>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Leave Type</label>
        <select name="leave_type" id="leave_type" class="form-control" required>
            <option selected value="">Choose...</option>
            @if(isset($leave))
                @foreach($leaveTypes as $type)
                <option value="{{ $type }}" {{ $leave->leave_type == $type ? 'selected' : '' }}>
                    {{ ucwords($type) }}
                </option>
                @endforeach
            @else
                @foreach($leaveTypes as $type)
                <option {{ old('leave_type') == $type ? 'selected' : '' }} value="{{ $type }}">
                    {{ ucwords($type) }}
                </option>
                @endforeach
            @endif
        </select>
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">From Date</label>
        <input type="date" class="form-control" name="from_date"
         value="{{ isset($leave) ? $leave->from_date->format('Y-m-d') : old('from_date') }}" required>
    </div>

    <div class="mb-3 col-lg-6">
        <label for="" class="form-label">To Date</label>
        <input type="date" class="form-control" name="to_date"
        value="{{ isset($leave) ? $leave->to_date->format('Y-m-d') : old('to_date') }}" required>
    </div>



    @if($mode == 'edit' & isset($leave))
        <div class="col-lg-6 mb-3">
            <label for="" class="form-label">Leave Days</label>
            <div class="form-control">{{ $leave->to_date->diffInDays($leave->from_date) }}</div>
        </div>

        <div class="col-lg-6 mb-3">
            <label for="" class="form-label">Status</label>
            <select name="status" id="status" class="form-control">
                <option selected value="">Choose...</option>
                @if(isset($leave))
                    @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ $leave->status == $status ? 'selected' : '' }}>
                        {{ ucwords($status) }}
                    </option>
                    @endforeach
                @else
                    @foreach($statuses as $status)
                    <option {{ old('type') == $status ? 'selected' : '' }} value="{{ $status }}">
                        {{ ucwords($status) }}
                    </option>
                    @endforeach
                @endif
            </select>
        </div>
    @endif


    <div class="mb-3">
        <label for="" class="form-label">Comment</label>
        <textarea name="comment" cols="30" rows="5" class="form-control" maxlength="250">{{ $leave->comment ?? old('comment') }}</textarea>
    </div>

</div>

<button type="submit" class="btn btn-primary">{{ $mode == 'create' ? 'Apply' : 'Update' }}</button>


@push('scripts')

<script>

    $(document).ready(() => {
        $('select').selectize();
        $('input[type=date]').flatpickr({
            altInput: true,
            altFormat: 'F j, Y',
            dateFormat: 'Y-m-d',
        });
    });

</script>

@endpush
