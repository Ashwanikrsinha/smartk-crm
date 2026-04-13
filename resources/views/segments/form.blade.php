@csrf
<div class="mb-3">
    <label for="" class="form-label">Name</label>
    <input type="text" class="form-control" name="name" value="{{ $segment->name ?? old('name') }}">
</div>

<div class="mb-3">
    <label for="" class="form-label">Category</label>
    <select name="category_id" id="category_id" class="form-control" required>
        <option selected value="">Choose...</option>
        @if(isset($segment))
        <option value="0" {{ $segment->category_id == 0 ? 'selected' : '' }}>Main Category</option>
        @foreach($categories as $key => $category)
        <option value="{{ $key }}" {{ $segment->category_id == $key ? 'selected' : '' }}>
            {{ $category }}
        </option>
        @endforeach
        @else
        <option value="0">Main Category</option>
        @foreach($categories as $key => $category)
        <option {{ old('category_id') == $key ? 'selected' : '' }} value="{{ $key }}">{{ $category }}</option>
        @endforeach
        @endif
    </select>
</div>

<button type="submit" class="btn btn-primary">{{ $mode == 'create' ? 'Save' : 'Edit' }}</button>


@push('scripts')

<script>
    $(document).ready(()=>{
      $('select').selectize();
   });
</script>

@endpush