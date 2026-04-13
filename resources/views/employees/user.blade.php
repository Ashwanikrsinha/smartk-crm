<h6 class="text-muted border-bottom pb-2 mb-4">User Information</h6>

<section class="row">
    
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Username</label>
        <input type="text" class="form-control" name="username" value="{{ $employee->name ?? old('username') }}"  data-input disabled>
    </div>

    
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Email Address</label>
        <input type="email" class="form-control" id="user-email"  value="{{ $employee->email ?? old('email') }}" disabled>
    </div>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Role</label>
        <select name="role_id"  class="form-control" data-input disabled>
            <option selected value="" disabled>Choose...</option>
            @foreach($roles as $id => $role)
            <option value="{{ $id }}">{{ $role }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Department</label>
        <select name="department_id"  class="form-control" data-input disabled>
            <option selected value="" disabled>Choose...</option>
            @foreach($departments as $id => $department)
             <option value="{{ $id }}" {{ old('department_id') == $id ? 'selected' : '' }}>{{ $department }}</option>
            @endforeach
        </select>
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Reportive To</label>
        <select name="reportive_id"  class="form-control" data-input disabled>
            <option selected value="" disabled>Choose...</option>
            @foreach($users as $id => $name)
            <option value="{{ $id }}" {{ old('reportive_id')==$id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
    </div>

</section>

<h6 class="text-muted border-bottom pb-2 mb-4">Confirm Password</h6>

<section class="row">
    <div class="col-lg-6 mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" autocomplete="off"  data-input disabled>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"autocomplete="off" data-input disabled>
    </div>
</section>



<div class="col-12 mb-4 ms-1">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="is_disable" name="is_disable" value="1">
        <label class="form-check-label" for="is_disable">Disable?</label>
    </div>
</div>
