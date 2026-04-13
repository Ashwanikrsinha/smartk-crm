@csrf
<section class="row">

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" value="{{ $employee->name ?? old('name') }}" required>
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Department</label>
        <select name="department_id"  class="form-control" required>
            <option selected value="">Choose...</option>
            @if(isset($employee))
            @foreach($departments as $id => $department)
            <option value="{{ $id }}" {{ $employee->department_id == $id ? 'selected': '' }}>
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


</section>

<h6 class="text-muted border-bottom pb-2 mb-4">Contact & Occasional Information</h6>

<section class="row mb-2">
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Email Address</label>
        <input type="email" class="form-control" name="email" value="{{ $employee->email ?? old('email') }}" required>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Phone Number</label>
        <input type="text" class="form-control" name="phone_number" value="{{ $employee->phone_number ?? old('phone_number') }}" required>
    </div>

    
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">State</label>
        <select name="state"  class="form-control">
            <option selected value="" disabled>Choose...</option>
            @if(isset($employee))
                @foreach($states as $state)
                <option value="{{ $state }}" {{ $employee->state == $state ? 'selected': '' }}>{{ $state }}</option>
                @endforeach
            @else
                @foreach($states as $state)
                <option value="{{ $state }}" {{ old('state') == $state ? 'selected': '' }}>{{ $state }}</option>
                @endforeach
            @endif
        </select>
    </div>

    
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">City</label>
        <input type="text" name="city" class="form-control" value="{{ $employee->city ?? old('city') }}">
    </div>
    
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Adrress</label>
        <input type="text" name="address" class="form-control" value="{{ $employee->address ?? old('address') }}">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Birth Date</label>
        <input type="date" name="birth_date" class="form-control"
            value="{{ isset($employee->birth_date) ? $employee->birth_date->format('Y-m-d') : old('birth_date') }}">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Marital Status</label>
        <select name="marital_status"  class="form-control">
            <option selected value="">Choose...</option>
            @if(isset($employee))
            @foreach($marital_statuses as $status)
            <option value="{{ $status }}" {{ $employee->marital_status == $status ? 'selected': '' }}>
                {{ ucfirst($status) }}
            </option>
            @endforeach
            @else
            @foreach($marital_statuses as $status)
            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
            @endforeach
            @endif
        </select>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Anniversary Date</label>
        <input type="date" name="marriage_date" class="form-control"
            value="{{isset($employee->marriage_date) ? $employee->marriage_date->format('Y-m-d') : old('marriage_date') }}">
    </div>

</section>

<h6 class="text-muted border-bottom pb-2 mb-4">Qualification & Experience</h6>

<section class="row mb-2">
    <div class="mb-3">
        <label for="" class="form-label">Qualification</label>
        @if (isset($employee))
        <textarea name="qualification" cols="30" rows="5" class="form-control">{{ $employee->qualification }}</textarea>
        @else
        <textarea name="qualification" cols="30" rows="5" class="form-control">
            <table class="table table-bordered" style="width: 100%; margin-bottom:1.5rem;text-align:center;">
                <thead>
                    <tr>
                        <th>Qualification</th>
                        <th style="min-width: 10rem;">Insitution</th>
                        <th>Board</th>
                        <th>Marks %</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 1; $i <= 3; $i++)
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
            <table class="table table-bordered" style="width: 100%;text-align:center;">
                <thead>
                    <tr>
                        <th>Experience</th>
                        <th>No. of Years</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Yes / Fresher</td>
                        <td>0</td>
                    </tr>
                </tbody>
            </table>
        </textarea>
        @endif
    </div>
</section>

<h6 class="text-muted border-bottom pb-2 mb-4">Salary & Allowance Information</h6>

<section class="row mb-2">
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Basic Salary</label>
        <input type="text" class="form-control" name="salary" value="{{ $employee->salary ?? old('salary') }}">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">HR Allowance</label>
        <input type="text" class="form-control" name="hr_allowance" value="{{ $employee->hr_allowance ?? old('hr_allowance') }}">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Convey Allowance</label>
        <input type="text" class="form-control" name="convey_allowance" value="{{ $employee->convey_allowance ?? old('convey_allowance') }}">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">SPI Allowance</label>
        <input type="text" class="form-control" name="spi_allowance" value="{{ $employee->spi_allowance ?? old('spi_allowance') }}">
    </div>
</section>

<h6 class="text-muted border-bottom pb-2 mb-4">Join & Resign Information</h6>

<section class="row mb-2">
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Joining Date</label>
        <input type="date" class="form-control" name="joining_date"
            value="{{ $employee->joining_date ?? old('joining_date') }}">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Resign Date</label>
        <input type="date" class="form-control" name="resign_date"
            value="{{ $employee->resign_date ?? old('resign_date') }}">
    </div>

    <div class="mb-3">
        <label for="" class="form-label">Reason For Resign</label>
        <textarea name="resign_reason" cols="30" rows="5" class="form-control"
            maxlength="150">{{ $employee->resign_reason ?? old('resign_reason') }}</textarea>
    </div>

    
</section>

<div class="mb-4">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="can_login" id="can-login" value="1">
        <label class="form-check-label" for="can-login">Employee Can Login?</label>
    </div>
</div>


<section id="user" class="d-none">
    @include('employees.user')
</section>


<button type="submit" class="btn btn-primary">{{ $mode == 'create' ? 'Save' : 'Edit' }}</button>

@push('scripts')

<script>

    function enableSelectize() {
        $('section#user').find('select').selectize({
            sortField: 'text'
        });
    }

    $(document).ready(()=>{

      $('select').selectize();

      tinymce.init({
            selector: '[name=qualification]',
            height: 420,
            branding: false,
            plugins: 'lists link image paste table fullscreen',
            toolbar: `undo redo | bold italic underline | alignleft
                    aligncenter alignright alignjustify | bullist numlist outdent indent 
                    | table |link image | fullscreen`,
        });

        
        $('#can-login[type=checkbox]').click(function(){

            $('section#user').find('select').each(function (el) {
                let value = $(this).val();
                $(this)[0].selectize.destroy();
                $(this).val(value);
            });

        
            if($(this).is(':checked')){

                $('section#user').removeClass('d-none'); 
                $('section#user').find('[data-input]').prop('required', true);
                $('section#user').find('[data-input]').prop('disabled', false);

            }
            else{
                $('section#user').addClass('d-none');
                $('section#user').find('[data-input]').prop('required', false);
                $('section#user').find('[data-input]').prop('disabled', true);
            }

            enableSelectize();

       });

       $('[name=name]').change(function(){
           $('[name=username]').val($(this).val());
       });

       $('[name=email]').change(function(){
           $('#user-email').val($(this).val());
       });




   });
</script>

@endpush