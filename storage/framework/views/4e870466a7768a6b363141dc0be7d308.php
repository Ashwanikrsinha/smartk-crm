
<?php $__env->startSection('content'); ?>

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>New <?php echo e(ucwords($type)); ?> Bill</h5>
    <a href="<?php echo e(route('bills.index', ['type' => $type])); ?>"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="<?php echo e(route('bills.store')); ?>" method="POST" 
class="p-3 shadow-sm rounded bg-white mb-5"
onsubmit="return confirm('Are You Sure?')">
    <?php echo $__env->make('bills.form', ['mode' => 'create'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</form>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/bills/create.blade.php ENDPATH**/ ?>