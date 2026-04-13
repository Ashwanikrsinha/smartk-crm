
<?php $__env->startSection('content'); ?>

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>New Product</h5>
    <a href="<?php echo e(route('products.index')); ?>"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="<?php echo e(route('products.store')); ?>" method="POST" class="p-3 shadow-sm rounded bg-white">
    <?php echo $__env->make('products.form', ['mode' => 'create'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</form>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/products/create.blade.php ENDPATH**/ ?>