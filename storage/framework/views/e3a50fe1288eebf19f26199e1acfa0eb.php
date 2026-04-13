<?php echo csrf_field(); ?>
<section class="row">

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" value="<?php echo e($employee->name ?? old('name')); ?>" required>
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Department</label>
        <select name="department_id"  class="form-control" required>
            <option selected value="">Choose...</option>
            <?php if(isset($employee)): ?>
            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($id); ?>" <?php echo e($employee->department_id == $id ? 'selected': ''); ?>>
                <?php echo e($department); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($id); ?>" <?php echo e(old('department_id')==$id ? 'selected' : ''); ?>><?php echo e($department); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>


</section>

<h6 class="text-muted border-bottom pb-2 mb-4">Contact & Occasional Information</h6>

<section class="row mb-2">
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Email Address</label>
        <input type="email" class="form-control" name="email" value="<?php echo e($employee->email ?? old('email')); ?>" required>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Phone Number</label>
        <input type="text" class="form-control" name="phone_number" value="<?php echo e($employee->phone_number ?? old('phone_number')); ?>" required>
    </div>

    
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">State</label>
        <select name="state"  class="form-control">
            <option selected value="" disabled>Choose...</option>
            <?php if(isset($employee)): ?>
                <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($state); ?>" <?php echo e($employee->state == $state ? 'selected': ''); ?>><?php echo e($state); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($state); ?>" <?php echo e(old('state') == $state ? 'selected': ''); ?>><?php echo e($state); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>

    
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">City</label>
        <input type="text" name="city" class="form-control" value="<?php echo e($employee->city ?? old('city')); ?>">
    </div>
    
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Adrress</label>
        <input type="text" name="address" class="form-control" value="<?php echo e($employee->address ?? old('address')); ?>">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Birth Date</label>
        <input type="date" name="birth_date" class="form-control"
            value="<?php echo e(isset($employee->birth_date) ? $employee->birth_date->format('Y-m-d') : old('birth_date')); ?>">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Marital Status</label>
        <select name="marital_status"  class="form-control">
            <option selected value="">Choose...</option>
            <?php if(isset($employee)): ?>
            <?php $__currentLoopData = $marital_statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($status); ?>" <?php echo e($employee->marital_status == $status ? 'selected': ''); ?>>
                <?php echo e(ucfirst($status)); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <?php $__currentLoopData = $marital_statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($status); ?>"><?php echo e(ucfirst($status)); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Anniversary Date</label>
        <input type="date" name="marriage_date" class="form-control"
            value="<?php echo e(isset($employee->marriage_date) ? $employee->marriage_date->format('Y-m-d') : old('marriage_date')); ?>">
    </div>

</section>

<h6 class="text-muted border-bottom pb-2 mb-4">Qualification & Experience</h6>

<section class="row mb-2">
    <div class="mb-3">
        <label for="" class="form-label">Qualification</label>
        <?php if(isset($employee)): ?>
        <textarea name="qualification" cols="30" rows="5" class="form-control"><?php echo e($employee->qualification); ?></textarea>
        <?php else: ?>
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
                    <?php for($i = 1; $i <= 3; $i++): ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endfor; ?>
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
        <?php endif; ?>
    </div>
</section>

<h6 class="text-muted border-bottom pb-2 mb-4">Salary & Allowance Information</h6>

<section class="row mb-2">
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Basic Salary</label>
        <input type="text" class="form-control" name="salary" value="<?php echo e($employee->salary ?? old('salary')); ?>">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">HR Allowance</label>
        <input type="text" class="form-control" name="hr_allowance" value="<?php echo e($employee->hr_allowance ?? old('hr_allowance')); ?>">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Convey Allowance</label>
        <input type="text" class="form-control" name="convey_allowance" value="<?php echo e($employee->convey_allowance ?? old('convey_allowance')); ?>">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">SPI Allowance</label>
        <input type="text" class="form-control" name="spi_allowance" value="<?php echo e($employee->spi_allowance ?? old('spi_allowance')); ?>">
    </div>
</section>

<h6 class="text-muted border-bottom pb-2 mb-4">Join & Resign Information</h6>

<section class="row mb-2">
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Joining Date</label>
        <input type="date" class="form-control" name="joining_date"
            value="<?php echo e($employee->joining_date ?? old('joining_date')); ?>">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Resign Date</label>
        <input type="date" class="form-control" name="resign_date"
            value="<?php echo e($employee->resign_date ?? old('resign_date')); ?>">
    </div>

    <div class="mb-3">
        <label for="" class="form-label">Reason For Resign</label>
        <textarea name="resign_reason" cols="30" rows="5" class="form-control"
            maxlength="150"><?php echo e($employee->resign_reason ?? old('resign_reason')); ?></textarea>
    </div>

    
</section>

<div class="mb-4">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="can_login" id="can-login" value="1">
        <label class="form-check-label" for="can-login">Employee Can Login?</label>
    </div>
</div>


<section id="user" class="d-none">
    <?php echo $__env->make('employees.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</section>


<button type="submit" class="btn btn-primary"><?php echo e($mode == 'create' ? 'Save' : 'Edit'); ?></button>

<?php $__env->startPush('scripts'); ?>

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

<?php $__env->stopPush(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/employees/form.blade.php ENDPATH**/ ?>