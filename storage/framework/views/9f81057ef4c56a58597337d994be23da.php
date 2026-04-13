<?php echo csrf_field(); ?>
<div class="mb-3">
    <label for="" class="form-label">Name</label>
    <input type="text" class="form-control" name="name" value="<?php echo e($role->name ?? old('name')); ?>" required>
</div>

<div class="mb-3">
    <label for="" class="form-label">Permissions</label>
    <select name="permissions[]" class="form-control mb-1" multiple required>
        <?php if(isset($role)): ?>
            <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($id); ?>" <?php echo e($role->permissions->contains($id) ? 'selected' : ''); ?>><?php echo e($permission); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <option selected value="">Choose...</option>
            <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($id); ?>"><?php echo e($permission); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </select>
</div>

<button type="submit" class="btn btn-primary"><?php echo e($mode == 'create' ? 'Save' : 'Edit'); ?></button>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(()=>{
        $('select').selectize({
            plugins: ["remove_button"],
        });
    });
    </script>
<?php $__env->stopPush(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/roles/form.blade.php ENDPATH**/ ?>