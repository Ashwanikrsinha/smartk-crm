<?php echo csrf_field(); ?>
<div class="row">
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Target</label>
        <input type="number" class="form-control" name="target" 
        value="<?php echo e($target->target ?? old('target')); ?>" placeholder="10" required>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Executive Name</label>
        <select name="user_id" id="user_id" class="form-control" required>
            <?php if(isset($target)): ?>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>" <?php echo e($target->user_id == $key ? 'selected' : ''); ?>>
                    <?php echo e($user); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>   
            <?php else: ?>
                <option selected value="">Choose...</option>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option <?php echo e(old('user_id') == $key ? 'selected' : ''); ?> value="<?php echo e($key); ?>"><?php echo e($user); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Start Date</label>
        <input type="date" name="start_date"
        value="<?php echo e(isset($target) ? $target->start_date->format('Y-m-d') : old('start_date')); ?>"
         class="form-control" required>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">End Date</label>
        <input type="date" name="end_date"
        value="<?php echo e(isset($target) ? $target->end_date->format('Y-m-d') : old('end_date')); ?>"
         class="form-control" required>
    </div>
    
</div>

<button type="submit" class="btn btn-primary"><?php echo e($mode == 'create' ? 'Create' : 'Edit'); ?></button>


<?php $__env->startPush('scripts'); ?>

<script>

    $(document).ready(() => {
        $('select').selectize(); 
        $('input[type=date]').flatpickr({
            altInput: true,
            dateFormat: 'Y-m-d',
        });  
    });

</script>

<?php $__env->stopPush(); ?>
<?php /**PATH D:\Data\smartk-crm\resources\views/targets/form.blade.php ENDPATH**/ ?>