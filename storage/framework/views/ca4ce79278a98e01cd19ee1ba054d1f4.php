<?php if($errors->any()): ?>

<section class="alert alert-danger alert-dismissible fade show">
    <ul class="list-unstyled mb-0">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <button class="btn btn-close" data-bs-dismiss="alert"></button>
</section>

<?php endif; ?><?php /**PATH D:\Data\smartk-crm\resources\views/partials/errors.blade.php ENDPATH**/ ?>