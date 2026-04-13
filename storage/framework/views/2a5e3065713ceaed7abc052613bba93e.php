<table class="table table-sm text-center" style="min-width: 30rem;">
    <thead>
       <tr class="text-secondary text-uppercase">
          <th class="fw-normal ps-3 text-start">Executive</th>
          <th class="fw-normal">Today</th>
          <th class="fw-normal">Yesterday</th>
          <th class="fw-normal">Last 7 Days</th>
          <th class="fw-normal">This Month</th>
          <th class="fw-normal pe-3">Last Month</th>
       </tr>
    </thead> 
 
       <?php
          $today_visits_total = 0;
          $yesterday_visits_total = 0;
          $last_seven_day_visits_total = 0;
          $current_month_visits_total = 0;
          $last_month_visits_total = 0;
       ?>
 
       <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
       
       <?php
          $today_visits_total += $user->today_visits_count;
          $yesterday_visits_total += $user->yesterday_visits_count;
          $last_seven_day_visits_total += $user->last_seven_days_visits_count;
          $current_month_visits_total += $user->current_month_visits_count;
          $last_month_visits_total += $user->last_month_visits_count;
       ?>
       <tbody>
          <tr>
             <td class="ps-3 text-start"><?php echo e($user->username); ?></td>
             <td><?php echo e($user->today_visits_count); ?></td>
             <td><?php echo e($user->yesterday_visits_count); ?></td>
             <td><?php echo e($user->last_seven_day_visits_count); ?></td>
             <td><?php echo e($user->current_month_visits_count); ?></td>
             <td class="pe-3"><?php echo e($user->last_month_visits_count); ?></td>
             <?php if($loop->last): ?>
                <tr class="fw-bold">
                   <td class="text-start ps-3">Total</td>
                   <td><?php echo e($today_visits_total); ?></td>
                   <td><?php echo e($yesterday_visits_total); ?></td>
                   <td><?php echo e($last_seven_day_visits_total); ?></td>
                   <td><?php echo e($current_month_visits_total); ?></td>
                   <td><?php echo e($last_month_visits_total); ?></td>
                </tr>
             <?php endif; ?>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    </table><?php /**PATH D:\Data\smartk-crm\resources\views/reports/visit-summary.blade.php ENDPATH**/ ?>