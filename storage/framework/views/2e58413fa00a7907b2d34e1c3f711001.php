
<?php $__env->startSection('content'); ?>

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>Edit Purpose</h5>
    <a href="<?php echo e(route('purposes.index')); ?>"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="<?php echo e(route('purposes.update', ['purpose' => $purpose ])); ?>" method="POST"
    class="p-3 shadow-sm rounded bg-white">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <div class="mb-3">
        <label for="" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" value="<?php echo e($purpose->name); ?>" required>
    </div>

    <button type="submit" class="btn btn-primary">Edit</button>
</form>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/purposes/edit.blade.php ENDPATH**/ ?>