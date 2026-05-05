@csrf
<section class="row">

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Username</label>
        <input type="text" class="form-control" name="username" value="{{ $user->username ?? old('username') }}"
            required>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Employee Code</label>
        <input type="text" class="form-control" name="emp_code" value="{{ $user->emp_code ?? old('emp_code') }}"
            required>
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Email Address</label>
        <input type="email" class="form-control" name="email" value="{{ $user->email ?? old('email') }}" required>
    </div>
@if(!auth()->user()->isSalesManager())
    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Role</label>
        <select name="role_id" id="" class="form-control" required>
            <option selected value="">Choose...</option>
            @if(isset($user))
            @foreach($roles as $id => $role)
            <option value="{{ $id }}" {{ $user->role_id == $id ? 'selected': '' }}>{{ $role }}</option>
            @endforeach
            @else
            @foreach($roles as $id => $role)
            <option value="{{ $id }}">{{ $role }}</option>
            @endforeach
            @endif
        </select>
    </div>
    @else
    <input type="hidden" name="role_id" value="3">
    @endif

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Department</label>
        <select name="department_id" id="" class="form-control" required>
            <option selected value="">Choose...</option>
            @if(isset($user))
            @foreach($departments as $id => $department)
            <option value="{{ $id }}" {{ $user->department_id == $id ? 'selected': '' }}>
                {{ $department }}
            </option>
            @endforeach
            @else
            @foreach($departments as $id => $department)
            <option value="{{ $id }}" {{ old('department_id')==$id ? 'selected' : '' }}>{{ $department }}</option>
            @endforeach
            @endif
        </select>
    </div>

@if(!auth()->user()->isSalesManager())
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Reportive To</label>
        <select name="reportive_id" id="" class="form-control">
            <option selected value="">Choose...</option>
            @if(isset($user))
            @foreach($users as $id => $name)
            @unless ($user->id == $id)
            <option value="{{ $id }}" {{ $user->reportive_id == $id ? 'selected': '' }}>{{ $name }}</option>
            @endunless
            @endforeach
            @else
            @foreach($users as $id => $name)
            <option value="{{ $id }}" {{ old('reportive_id')==$id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
            @endif
        </select>
    </div>
    @else
    <input type="hidden" name="reportive_id" value="{{ auth()->user()->id }}">
    @endif

</section>

<h6 class="fw-bold border-bottom pb-2 mb-4"> {{$mode == 'edit' ? 'Change' : 'Confirm' }} Password</h6>

<section class="row">
    <div class="col-lg-6 mb-3">
        <label for="password" class="form-label">Password @if($mode == 'edit') (Optional) @endif </label>
        <input type="password" name="password" id="password" class="form-control" autocomplete="off" {{ $mode=='edit'
            ? '' : 'required' }}>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
            autocomplete="off">
    </div>
</section>


<div class="col-12 mb-4 ms-1">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="is_disable" name="is_disable"
        value="1" {{ isset($user) ? $user->is_disable ? 'checked' : '' : '' }}>
        <label class="form-check-label" for="is_disable">Disable?</label>
    </div>
</div>

<button type="submit" class="btn btn-primary">{{ $mode == 'create' ? 'Save' : 'Update' }}</button>

@push('scripts')

<script>
    $(document).ready(()=>{

      $('select').selectize();

   });
</script>

@endpush
