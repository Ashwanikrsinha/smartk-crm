<?php $__env->startSection('content'); ?>
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">New Purchase Order</h5>
            <small class="text-muted">Fill in the school and order details below</small>
        </div>
        <a href="<?php echo e(route('invoices.index')); ?>" class="btn btn-sm btn-secondary">
            <i class="feather icon-arrow-left me-1"></i> Back
        </a>
    </header>

    <form action="<?php echo e(route('invoices.store')); ?>" method="POST" id="po-form" enctype="multipart/form-data">
        <?php echo $__env->make('invoices.form', ['mode' => 'create'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </form>

    
    <?php echo $__env->make('invoices.partials.new-school-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/invoices/create.blade.php ENDPATH**/ ?>