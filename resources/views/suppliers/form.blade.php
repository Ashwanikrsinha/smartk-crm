@csrf
<div class="row">
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" value="{{ $supplier->name ?? old('name') }}">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Supplier Type</label>
        <select name="supplier_types[]" class="form-control mb-1" multiple>
            <option value="" selected disabled>Choose...</option>
            @if (isset($supplier))
                @foreach($supplier_types as $key => $type)
                    <option {{ in_array($key, explode(',', $supplier->supplier_types)) ? 'selected' : '' }}
                         value="{{ $key }}">
                        {{ $type }}
                    </option>
                @endforeach
            @else
                @foreach($supplier_types as $key => $type)
                <option value="{{ $key }}">{{ $type }}</option>
                @endforeach
            @endif
        </select>
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Segment</label>
        <select name="segment_id" id="segment_id" class="form-control" required>
            <option selected value="">Choose...</option>
            @if (isset($supplier))
                @foreach($segments as $key => $segment)
                <option {{ $supplier->segment_id == $key ? 'selected' : '' }} value="{{ $key }}">{{ $segment }}</option>
                @endforeach
            @else
                @foreach($segments as $key => $segment)
                <option {{ old('segment_id') == $key ? 'selected' : '' }} value="{{ $key }}">{{ $segment}}</option>
                @endforeach
            @endif
        </select>
    </div>


    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">State</label>
        <select name="state" class="form-control">
            <option selected value="">Choose...</option>
            @if (isset($supplier))
                @foreach($states as $state)
                <option value="{{ $state }}" {{ $supplier->state == $state ? 'selected' : '' }}>{{ $state }}</option>
                @endforeach
            @else
                @foreach($states as $state)
                <option value="{{ $state }}">{{ $state }}</option>
                @endforeach
            @endif
        </select>
    </div>



    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">City</label>
        <input type="text" class="form-control" name="city" value="{{ $supplier->city ?? old('city') }}">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Address</label>
        <input type="text" class="form-control" name="address" value="{{ $supplier->address ?? old('address') }}">
    </div>


    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">GST No.</label>
        <input type="text" class="form-control" name="gst_number" value="{{ $supplier->gst_number ?? old('gst_number') }}">
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Email</label>
        <input type="text" class="form-control" name="email" value="{{ $supplier->email ?? old('email') }}">
    </div>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Phone No.</label>
        <input type="text" class="form-control" name="phone_number" value="{{ $supplier->phone_number ?? old('phone_number') }}">
    </div>



    @include('suppliers.contacts')

    <div class="mb-3">
        <label for="" class="form-label">Description</label>
        <textarea name="description" cols="30" rows="5" class="form-control">{{  $supplier->description ?? old('description') }}</textarea>
    </div>

</div>

<button type="submit" class="btn btn-primary">{{ $mode == 'create' ? 'Save' : 'Update' }}</button>

@push('scripts')

<script>
    function enableSelectize() {
        $('table#contact tbody').find('select').selectize({
            sortField: 'text'
        });
    }

    $(document).ready(() => {

        $('select').selectize();


        $('#add-row').click(function (e) {

            e.preventDefault();

            $('table#contact tbody tr:last').find('select').each(function (el) {
                let value = $(this).val();
                $(this)[0].selectize.destroy();
                $(this).val(value);
            });

            $('table#contact tbody tr:last').clone()
                .appendTo('table#contact tbody')
                .find('[name]').val('');

            enableSelectize();
        });


        // remove last row
        $('#remove-row').on('click', (e) => {
            e.preventDefault();
            $('table#contact tbody tr:last').remove();
        });


    });

</script>

@endpush
