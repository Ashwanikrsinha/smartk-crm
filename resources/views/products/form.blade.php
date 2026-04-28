@csrf

<div class="row">


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" value="{{ $product->name ?? old('name') }}">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">HSN Code</label>
        <input type="tel" class="form-control" name="code" value="{{ $product->code ?? old('code') }}">
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Unit</label>
        <select name="unit_id" id="" class="form-control" required>
            <option selected value="">Choose...</option>
            @if(isset($product))
                @foreach($units as $id => $unit)
                <option value="{{ $id }}" {{ $product->unit_id == $id ? 'selected': '' }}>{{ $unit }}</option>
                @endforeach
            @else
                @foreach($units as $id => $unit)
                <option value="{{ $id }}">{{ $unit }}</option>
                @endforeach
            @endif
        </select>
    </div>

    {{-- <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Group</label>
        <select name="group_id" id="" class="form-control" required>
            <option selected value="">Choose...</option>
            @if(isset($product))
                @foreach($groups as $id => $group)
                <option value="{{ $id }}" {{ $product->group_id == $id ? 'selected': '' }}>{{ $group }}</option>
                @endforeach
            @else
                @foreach($groups as $id => $group)
                <option value="{{ $id }}">{{ $group }}</option>
                @endforeach
            @endif
        </select>
    </div> --}}

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Product Type</label>
        <select name="category_id" id="" class="form-control" required>
            <option selected value="">Choose...</option>
            @if(isset($product))
                @foreach($categories as $id => $category)
                <option value="{{ $id }}" {{ $product->category_id == $id ? 'selected': '' }}>{{ $category }}</option>
                @endforeach
            @else
                @foreach($categories as $id => $category)
                <option value="{{ $id }}">{{ $category }}</option>
                @endforeach
            @endif
        </select>
    </div>


    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Price</label>
        <input type="number" class="form-control" name="price" value="{{ $product->price ?? old('price') }}" required>
    </div>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Re Order Level</label>
        <input type="number" class="form-control" name="reorder_level" value="{{ $product->reorder_level ?? old('reorder_level') }}" required>
    </div>

</div>



<div id="filepond-alert" class="alert alert-danger d-none my-3">
Only images: jpg, png, jpeg files are allowed with max size 10MB.
</div>

<div class="mb-3">
    <label for="images" class="form-label">Images</label>
    <input type="file" name="images[]" multiple max="3" id="images">
</div>


<div class="mb-3">
    <label for="" class="form-label">Description</label>
    <textarea name="description" cols="30" rows="5" class="form-control">{{ $product->description ?? old('description') }}</textarea>
</div>



<button type="submit" class="btn btn-primary">{{ $mode == 'create' ? 'Save' : 'Update' }}</button>

@push('scripts')

<script>


    $(document).ready(() => {

        $('select').selectize();

        const filePondAlertEl = $('#filepond-alert');

        FilePond.create(document.querySelector('#images'));

        FilePond.setOptions({
            server : {
                headers : {
                      'X-CSRF-TOKEN' : '{{ csrf_token() }}',
                      'X-Requested-With': 'XMLHttpRequest',
                },
                process : {
                    url : `{{ route('products.images.store') }}`,
                    onerror : (res) => {
                        console.log(res);
                        filePondAlertEl.removeClass('d-none');
                        setTimeout(() => filePondAlertEl.addClass('d-none'), 4000);
                    }
                },
                revert: {
                  url : `{{ route('products.images.destroy') }}`,
                  _method : 'DELETE',
                }
            }
        })



    });

</script>

@endpush
