
<?php $__env->startSection('content'); ?>

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>User</h5>
  <a href="<?php echo e(route('users.index')); ?>" class="btn btn-secondary btn-sm">Back</a>
</header>

<div class="card border-0 shadow-sm mb-4">
  <header class="card-header bg-white py-3">
      <i class="feather icon-user me-1 bg-primary text-white rounded p-1"></i>
  </header>
<div class="card-body px-0 pt-0">
  <section class="table-responsive rounded">
    <table class="table" style="min-width: 40rem;">
      <tbody>
        <tr>
          <th class="ps-3">Username</th>
          <td><?php echo e($user->username); ?></td>
        </tr>
        <tr>
          <th class="ps-3">Email Address</th>
          <td><?php echo e($user->email); ?></td>
        </tr>
        <tr>
          <th class="ps-3">Role</th>
          <td><?php echo e($user->role->name); ?></td>
        </tr>
        <tr>
          <th class="ps-3">Department</th>
          <td><?php echo e($user->department->name); ?></td>
        </tr>
        <tr>
          <th class="ps-3">Reportive To</th>
          <td><?php echo e(isset($user->reportiveTo) ? $user->reportiveTo->username : ''); ?></td>
        </tr>
        <tr>
          <th class="ps-3">Status</th>
          <td>
            <?php if($user->is_disable): ?>
              Disabled <i class="feather icon-x-circle text-danger ms-1"></i>
              - 
              <span>Inactive Date : <?php echo e($user->inactive_date->format('d M, Y')); ?></span>
            <?php else: ?>
             Active <i class="feather icon-check-circle text-success ms-1"></i>
            <?php endif; ?>
          </td>
        </tr>
      </tbody>
    </table>
  </section>
</div>
</div>

  <?php if($logs->count() > 0 && auth()->user()->hasPermission('browse_logs')): ?>
  <section class="table-responsive rounded shadow-sm rounded bg-white mb-4">
    <table class="table" id="logs">
      <thead>
        <tr>
          <th colspan="3" class="ps-3 py-3 fw-normal">
            <i class="feather icon-user me-1 bg-primary text-white rounded p-1"></i>
            Logs
          </th>
        </tr>
        <tr>
          <th class="ps-3">IP Address</th>
          <th class="ps-3">Login Time</th>
          <th class="ps-3">Logout Time</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td class="ps-3"><?php echo e($log->ip_address); ?></td>
          <td><?php echo e($log->login_time->format('d M, Y - h:i:s A')); ?></td>
          <td><?php echo e(isset($log->logout_time) ? $log->logout_time->format('d M, Y - h:i:s A') : ''); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
    <?php echo e($logs->links()); ?>

  </section>
  <?php endif; ?>

</div>

<?php $__env->stopSection(); ?>


<?php $__env->startPush('scripts'); ?>

<script>
    $(document).ready(() => {
      
      $('#qualification table').each(function(){ $(this).addClass('table table-bordered w-100')});

    });
</script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/users/show.blade.php ENDPATH**/ ?>