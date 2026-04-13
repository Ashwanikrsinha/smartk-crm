@csrf
<div class="mb-3">
    <label for="" class="form-label">Name</label>
    <input type="text" class="form-control" name="name" value="{{ $role->name ?? old('name') }}" required>
</div>

<div class="mb-3">
    <label for="" class="form-label">Permissions</label>
    <select name="permissions[]" class="form-control mb-1" multiple required>
        @if(isset($role))
            @foreach($permissions as $id => $permission)
            <option value="{{ $id }}" {{ $role->permissions->contains($id) ? 'selected' : '' }}>{{ $permission }}</option>
            @endforeach
        @else
            <option selected value="">Choose...</option>
            @foreach($permissions as $id => $permission)
            <option value="{{ $id }}">{{ $permission }}</option>
            @endforeach
        @endif
    </select>
</div>

<button type="submit" class="btn btn-primary">{{ $mode == 'create' ? 'Save' : 'Edit' }}</button>

@push('scripts')
    <script>
        $(document).ready(()=>{
        $('select').selectize({
            plugins: ["remove_button"],
        });
    });
    </script>
@endpush