<?php if(session('success')): ?>
  
  <article class="alert bg-white shadow-sm fade show align-items-center justify-content-between"
   id="success" style="display: flex;">
      <span>
        <i class="feather icon-check-circle bg-primary p-1 rounded me-1 text-white"></i>
        <?php echo e(session('success')); ?>

      </span>
      <button class="btn btn-close" data-bs-dismiss="alert"></button>
  </article>

<?php endif; ?>

<?php /**PATH D:\Data\smartk-crm\resources\views/partials/success.blade.php ENDPATH**/ ?>