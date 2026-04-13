
<?php $__env->startSection('content'); ?>

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>New Visit</h5>
    <a href="<?php echo e(route('visits.index')); ?>"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="<?php echo e(route('visits.store')); ?>" method="POST" class="p-3 shadow-sm rounded bg-white">
    <?php echo $__env->make('visits.form', ['mode' => 'create'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</form>

<?php echo $__env->make('customers.modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/visits/create.blade.php ENDPATH**/ ?>