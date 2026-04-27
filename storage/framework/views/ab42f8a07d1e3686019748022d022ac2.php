<?php echo csrf_field(); ?>
<section class="row">

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Username</label>
        <input type="text" class="form-control" name="username" value="<?php echo e($user->username ?? old('username')); ?>"
            required>
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Email Address</label>
        <input type="email" class="form-control" name="email" value="<?php echo e($user->email ?? old('email')); ?>" required>
    </div>
<?php if(!auth()->user()->isSalesManager()): ?>
    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Role</label>
        <select name="role_id" id="" class="form-control" required>
            <option selected value="">Choose...</option>
            <?php if(isset($user)): ?>
            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($id); ?>" <?php echo e($user->role_id == $id ? 'selected': ''); ?>><?php echo e($role); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($id); ?>"><?php echo e($role); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>
    <?php else: ?>
    <input type="hidden" name="role_id" value="3">
    <?php endif; ?>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Department</label>
        <select name="department_id" id="" class="form-control" required>
            <option selected value="">Choose...</option>
            <?php if(isset($user)): ?>
            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($id); ?>" <?php echo e($user->department_id == $id ? 'selected': ''); ?>>
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

<?php if(!auth()->user()->isSalesManager()): ?>
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Reportive To</label>
        <select name="reportive_id" id="" class="form-control">
            <option selected value="">Choose...</option>
            <?php if(isset($user)): ?>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if (! ($user->id == $id)): ?>
            <option value="<?php echo e($id); ?>" <?php echo e($user->reportive_id == $id ? 'selected': ''); ?>><?php echo e($name); ?></option>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($id); ?>" <?php echo e(old('reportive_id')==$id ? 'selected' : ''); ?>><?php echo e($name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>
    <?php else: ?>
    <input type="hidden" name="reportive_id" value="<?php echo e(auth()->user()->id); ?>">
    <?php endif; ?>

</section>

<h6 class="fw-bold border-bottom pb-2 mb-4"> <?php echo e($mode == 'edit' ? 'Change' : 'Confirm'); ?> Password</h6>

<section class="row">
    <div class="col-lg-6 mb-3">
        <label for="password" class="form-label">Password <?php if($mode == 'edit'): ?> (Optional) <?php endif; ?> </label>
        <input type="password" name="password" id="password" class="form-control" autocomplete="off" <?php echo e($mode=='edit'
            ? '' : 'required'); ?>>
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
        value="1" <?php echo e(isset($user) ? $user->is_disable ? 'checked' : '' : ''); ?>>
        <label class="form-check-label" for="is_disable">Disable?</label>
    </div>
</div>

<button type="submit" class="btn btn-primary"><?php echo e($mode == 'create' ? 'Save' : 'Update'); ?></button>

<?php $__env->startPush('scripts'); ?>

<script>
    $(document).ready(()=>{

      $('select').selectize();

   });
</script>

<?php $__env->stopPush(); ?>
<?php /**PATH D:\Data\smartk-crm\resources\views/users/form.blade.php ENDPATH**/ ?>