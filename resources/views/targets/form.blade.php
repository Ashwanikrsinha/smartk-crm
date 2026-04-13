@csrf
<div class="row">
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Target</label>
        <input type="number" class="form-control" name="target" 
        value="{{ $target->target ?? old('target') }}" placeholder="10" required>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Executive Name</label>
        <select name="user_id" id="user_id" class="form-control" required>
            @if(isset($target))
                @foreach($users as $key => $user)
                <option value="{{ $key }}" {{ $target->user_id == $key ? 'selected' : '' }}>
                    {{ $user }}
                </option>
                @endforeach   
            @else
                <option selected value="">Choose...</option>
                @foreach($users as $key => $user)
                    <option {{ old('user_id') == $key ? 'selected' : '' }} value="{{ $key }}">{{ $user}}</option>
                @endforeach
            @endif
        </select>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Start Date</label>
        <input type="date" name="start_date"
        value="{{ isset($target) ? $target->start_date->format('Y-m-d') : old('start_date') }}"
         class="form-control" required>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">End Date</label>
        <input type="date" name="end_date"
        value="{{ isset($target) ? $target->end_date->format('Y-m-d') : old('end_date') }}"
         class="form-control" required>
    </div>
    
</div>

<button type="submit" class="btn btn-primary">{{ $mode == 'create' ? 'Create' : 'Edit' }}</button>


@push('scripts')

<script>

    $(document).ready(() => {
        $('select').selectize(); 
        $('input[type=date]').flatpickr({
            altInput: true,
            dateFormat: 'Y-m-d',
        });  
    });

</script>

@endpush
