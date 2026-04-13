<div class="card border-0 shadow-sm bg-white mb-4">
    <header class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
       <span>
          <i class="feather icon-bookmark me-1 bg-primary text-white rounded p-1"></i>
          Staff On Leave - Today
       </span>
       <span class="badge bg-danger rounded-pill"><?php echo e($leaves->count()); ?></span>
    </header>
    <div class="card-body px-0 pt-0 table-responsive" style="max-height: 20rem; overflow-y: auto;">
       <?php if($leaves->count() > 0): ?>
       <table class="table" style="min-width: 30rem;">
          <thead>
             <tr class="text-uppercase text-muted small">
                <th></th>
                <th class="fw-normal">Leave</th>
                <th class="fw-normal">From - To</th>
                <th class="fw-normal">Status</th>
                <th class="fw-normal">Executive</th>
             </tr>
          </thead>
          <tbody>
             <?php $__currentLoopData = $leaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                 <tr>
                   <td>
                      <a href="<?php echo e(route('leaves.show', ['leave' => $leave])); ?>" target="_blank">
                        <i class="feather icon-external-link me-1 text-muted"></i>
                      </a>
                   </td>
                    <td><?php echo e($leave->leave_number); ?></td>
                    <td><?php echo e($leave->from_date->format('d M')); ?> - <?php echo e($leave->to_date->format('d M')); ?></td>
                    <td>
                       <span class="badge alert-primary"><?php echo e($leave->status); ?></span>
                    </td>
                    <td><?php echo e(Str::limit($leave->user->username, 20)); ?></td>
                 </tr>
             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
       </table>
      <?php endif; ?>
    </div>
 </div><?php /**PATH D:\Data\smartk-crm\resources\views/widgets/leave.blade.php ENDPATH**/ ?>