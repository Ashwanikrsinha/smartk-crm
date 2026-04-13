<div class="card border-0 shadow-sm mb-4">
    <header class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
      <span>
       <i class="feather icon-message-square me-1 bg-primary text-white rounded p-1"></i>
       Customers Occassions
      </span>
      <span class="badge bg-danger rounded-pill"><?php echo e($contact_occassions->count()); ?></span>
    </header>
    <div class="card-body px-0 pt-0 table-responsive">
       <?php if($contact_occassions->count() > 0): ?>
       <table class="table align-middle" style="min-width: 30rem;">
          <thead>
             <tr class="text-uppercase text-muted small">
                <th class="fw-normal ps-3">Contact Name</th>
                <th class="fw-normal">Occassion</th>
             </tr>
          </thead>
          <tbody>
             <?php $__currentLoopData = $contact_occassions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                 <tr>
                    <td class="ps-3">
                       <p class="mb-0"><?php echo e($contact->name); ?> &bull; <?php echo e($contact->designation); ?></p>
                       <small class="text-muted">(<?php echo e($contact->name); ?>)</small>
                   </td>
                    <td>
                      <p class="mb-1">
                         <?php if(isset($contact->birth_date) && $contact->birth_date->format('m-d') == date('m-d')): ?>  
                         <?php echo e($contact->birth_date->format('d M, Y')); ?>

                         <span class="badge alert-primary ms-1">Birthday</span>
                         <?php endif; ?>
                      </p>

                       <?php if(isset($contact->marriage_date) && $contact->marriage_date->format('m-d') == date('m-d')): ?>  
                       <?php echo e($contact->marriage_date->format('d M , Y')); ?>

                       <small class="badge alert-primary ms-1">Marriage</small>
                      <?php endif; ?>
                   </td>
                 </tr>
             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
       </table>
      <?php endif; ?>
    </div>
 </div><?php /**PATH D:\Data\smartk-crm\resources\views/widgets/customer-occasion.blade.php ENDPATH**/ ?>