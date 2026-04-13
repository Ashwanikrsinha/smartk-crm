@csrf
<div class="row">

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" value="{{ $transport->name ?? old('name') }}">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Phone No.</label>
        <input type="tel" class="form-control" name="phone_number" value="{{ $transport->phone_number ?? old('phone_number') }}">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">GST No.</label>
        <input type="text" class="form-control" name="gst_number" value="{{ $transport->gst_number ?? old('gst_number') }}">
    </div>
    

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Address</label>
        <input type="text" class="form-control" name="address" value="{{ $transport->address ?? old('address') }}">
    </div>

</div>

<button type="submit" class="btn btn-primary">{{ $mode == 'create' ? 'Save' : 'Edit' }}</button>
