<h6 class="text-muted border-bottom pb-2 mb-4">User Information</h6>

<section class="row">
    
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Username</label>
        <input type="text" class="form-control" name="username" value="<?php echo e($employee->name ?? old('username')); ?>"  data-input disabled>
    </div>

    
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Email Address</label>
        <input type="email" class="form-control" id="user-email"  value="<?php echo e($employee->email ?? old('email')); ?>" disabled>
    </div>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Role</label>
        <select name="role_id"  class="form-control" data-input disabled>
            <option selected value="" disabled>Choose...</option>
            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($id); ?>"><?php echo e($role); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Department</label>
        <select name="department_id"  class="form-control" data-input disabled>
            <option selected value="" disabled>Choose...</option>
            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
             <option value="<?php echo e($id); ?>" <?php echo e(old('department_id') == $id ? 'selected' : ''); ?>><?php echo e($department); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Reportive To</label>
        <select name="reportive_id"  class="form-control" data-input disabled>
            <option selected value="" disabled>Choose...</option>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($id); ?>" <?php echo e(old('reportive_id')==$id ? 'selected' : ''); ?>><?php echo e($name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php /**PATH D:\Data\smartk-crm\resources\views/employees/user.blade.php ENDPATH**/ ?>