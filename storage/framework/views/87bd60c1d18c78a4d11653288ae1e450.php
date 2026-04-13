<div class="card border-0 shadow-sm bg-white mb-4">
    <header class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
       <span>
          <i class="feather icon-bookmark me-1 bg-primary text-white rounded p-1"></i>
          Employees Occasions
       </span>
       <span class="badge bg-danger rounded-pill"><?php echo e($employee_occassions->count()); ?></span>
    </header>
    <div class="card-body px-0 pt-0 table-responsive">
       <?php if($employee_occassions->count() > 0): ?>
       <table class="table align-middle" style="min-width: 30rem;">
          <thead>
             <tr class="text-uppercase text-muted small">
                <th class="fw-normal ps-3">Name</th>
                <th class="fw-normal">Occassion</th>
             </tr>
          </thead>
          <tbody>
             <?php $__currentLoopData = $employee_occassions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                 <tr>
                    <td class="ps-3">
                       <p class="mb-0"><?php echo e($employee->name); ?></p>
                       <small class="text-muted">(<?php echo e($employee->department->name); ?>)</small>
                   </td>
                    <td>
                      <p class="mb-1">
                         <?php if(isset($employee->birth_date) && $employee->birth_date->format('m-d') == date('m-d')): ?>  
                         <?php echo e($employee->birth_date->format('d M, Y')); ?>

                         <span class="badge alert-primary ms-1">Birthday</span>
                         <?php endif; ?>
                      </p>

                       <?php if(isset($employee->marriage_date) && $employee->marriage_date->format('m-d') == date('m-d')): ?>  
                       <?php echo e($employee->marriage_date->format('d M, Y')); ?>

                       <small class="badge alert-primary ms-1">Marriage</small>
                      <?php endif; ?>
                   </td>
                 </tr>
             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
       </table>
      <?php endif; ?>
    </div>
 </div><?php /**PATH D:\Data\smartk-crm\resources\views/widgets/employee-occasion.blade.php ENDPATH**/ ?>