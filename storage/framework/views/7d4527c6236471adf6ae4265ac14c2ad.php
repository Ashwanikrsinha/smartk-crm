<div class="card border-0 shadow-sm bg-white mb-4">
    <header class="card-header bg-white py-3">
       <i class="feather icon-sliders me-1 bg-primary text-white rounded p-1"></i> Visits Summary
    </header>
    <div class="card-body px-0 pt-0 table-responsive" id="visit-summary">
       <div class="d-flex align-items-center justify-content-center" style="min-height: 25rem;">
          <div class="spinner-grow text-primary" role="status"></div>
       </div>
    </div>
 </div>   
 
 <?php $__env->startPush('scripts'); ?>
  <script>
     $(document).ready(()=>{
       
       $.ajax({
             url : `<?php echo e(route('visits.summary')); ?>`,
             dataType : 'html'
       })
       .done((data)=>{ 
          $('#visit-summary').html(data);
          $('#visit-summary .d-flex').remove();
       })
       .fail((error) => console.log(error));
    });
  </script>
 <?php $__env->stopPush(); ?>
 <?php /**PATH D:\Data\smartk-crm\resources\views/widgets/visit-summary.blade.php ENDPATH**/ ?>