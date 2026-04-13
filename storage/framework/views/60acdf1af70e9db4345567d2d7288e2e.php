
<?php $__env->startSection('content'); ?>

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Visits Aging - Report</h5>
  <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-sm btn-secondary">Back</a>
</header>

<?php if(isset($visits)): ?>
<section class="table-responsive rounded shadow-sm rounded bg-white mb-4">
  <table class="table">
    <thead>
      <tr>
        <th class="ps-3">Visit Date</th>
        <th>Customer Name</th>
        <th>No. of Days From Last Visit</th>
      </tr>
    </thead>
    <tbody>
      <?php $__currentLoopData = $visits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr>
        <td class="ps-3"><?php echo e($visit->visit_date->format('d M, Y')); ?></td>
        <td><?php echo e($visit->customer->name); ?></td>
        <td><?php echo e($visit->total_days); ?></td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
  <div class="d-flex justify-content-end">
    <?php echo e($visits->links()); ?>

  </div>
</section>
<?php endif; ?>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/reports/visits-aging.blade.php ENDPATH**/ ?>