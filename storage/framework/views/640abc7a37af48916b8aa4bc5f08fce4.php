
<?php $__env->startSection('content'); ?>


<?php if(!file_exists(public_path('storage'))): ?>
<div class="alert bg-white shadow-sm  d-flex align-items-center justify-content-between py-2 mb-4">
   <span>
      <i class="feather icon-link bg-primary text-white rounded p-1 me-1"></i>
      Storage link not found.
   </span>
   <form action="<?php echo e(route('storage.link.create')); ?>" method="GET" class="d-inline-block">
      <button class="btn btn-primary">Create</button>
   </form>
</div>
<?php endif; ?>


<?php if($notification_count > 0): ?>  
   <div class="alert bg-white shadow-sm mb-3 d-flex align-items-center justify-content-between">
      <span>
         <i class="feather icon-bell bg-primary p-1 rounded me-1 text-white"></i>
         You has <span class="badge bg-danger rounded-pill mx-1"><?php echo e($notification_count); ?></span> new notifications
      </span>
      <a href="<?php echo e(route('notifications.index')); ?>" class="btn btn-primary">Notifications</a>
   </div>
<?php endif; ?>


<?php if(isset(auth()->user()->birth_date) && auth()->user()->birth_date->format('m-d') == date('m-d')): ?>
   <article class="alert bg-white shadow-sm fade show align-items-center justify-content-between" style="display: flex;">
      <span class="d-flex align-items-center">
         <i class="feather icon-heart-on text-danger fs-4 me-1"></i> 
         <span>
            Happy Birthday <span class="text-primary"><?php echo e(auth()->user()->username); ?></span>
         </span>
      </span>
      <button class="btn btn-close" data-bs-dismiss="alert"></button>
   </article>
<?php endif; ?>


<?php if(isset(auth()->user()->marriage_date) && auth()->user()->marriage_date->format('m-d') == date('m-d')): ?>
<article class="alert bg-white shadow-sm fade show align-items-center justify-content-between" style="display: flex;">
   <span class="d-flex align-items-center">
      <i class="feather icon-heart-on text-danger fs-4 me-1"></i> 
      <span>
         Happy Marriage Anniversery 
         <span class="text-primary"><?php echo e(auth()->user()->username); ?></span>
      </span>
   </span>
   <button class="btn btn-close" data-bs-dismiss="alert"></button>
</article>
<?php endif; ?>


<section class="row">

   <div class="col-md-6 col-lg-3">

      <article class="bg-white rounded shadow-sm px-3 py-4 mb-4">
         
         <div class="d-flex align-items-center justify-content-between mb-2">
            <div>      
               <i class="feather icon-dollar-sign text-white p-1 rounded-pill bg-primary"></i>
               <h4 class="d-inline-block ms-1">Sales</h4>
            </div>
            <i class="feather icon-bar-chart text-success fs-5"></i>
         </div>
         
         <div class="d-flex align-items-center justify-content-between">
            <h6 class="fw-bold">$125000.00</h6>
            <span class="text-success">+18%</span>
         </div>

         <small class="text-muted">Compared to 50000.00</small>
      </article>

   </div>

   <div class="col-md-6 col-lg-3">

      <article class="bg-white rounded shadow-sm px-3 py-4 mb-4">
         
         <div class="d-flex align-items-center justify-content-between mb-2">
            <div>      
               <i class="feather icon-dollar-sign text-white p-1 rounded-pill bg-success"></i>
               <h4 class="d-inline-block ms-1">Purchase</h4>
            </div>
            <i class="feather icon-pie-chart text-success fs-5"></i>
         </div>
         
         <div class="d-flex align-items-center justify-content-between">
            <h6 class="fw-bold">$75000.00</h6>
            <span class="text-danger">-21%</span>
         </div>

         <small class="text-muted">Compared to 950000.00</small>
      </article>

   </div>
    
   <div class="col-md-6 col-lg-3">

      <article class="bg-white rounded shadow-sm px-3 py-4 mb-4">
         
         <div class="d-flex align-items-center justify-content-between mb-2">
            <div>      
               <i class="feather icon-dollar-sign text-white p-1 rounded-pill bg-warning"></i>
               <h4 class="d-inline-block ms-1">Payable</h4>
            </div>
            <i class="feather icon-bar-chart-line text-success fs-5"></i>
         </div>
         
         <div class="d-flex align-items-center justify-content-between">
            <h6 class="fw-bold">$25000.00</h6>
            <span class="text-success">+18%</span>
         </div>

         <small class="text-muted">Compared to 30000.00</small>
      </article>

   </div>

   <div class="col-md-6 col-lg-3">

      <article class="bg-white rounded shadow-sm px-3 py-4 mb-4">
         
         <div class="d-flex align-items-center justify-content-between mb-2">
            <div>      
               <i class="feather icon-dollar-sign text-white p-1 rounded-pill bg-danger"></i>
               <h4 class="d-inline-block ms-1">Receivable</h4>
            </div>
            <i class="feather icon-arrow-up text-success fs-5"></i>
         </div>
         
         <div class="d-flex align-items-center justify-content-between">
            <h6 class="fw-bold">$125000.00</h6>
            <span class="text-success">+18%</span>
         </div>

         <small class="text-muted">Compared to 230000.00</small>
      </article>

   </div>


</section>


<section class="row">

   <div class="col-md-6">
      <article class="alert-primary p-3 rounded mb-4 shadow-sm d-flex align-items-center justify-content-between">
         <div>      
            <i class="feather icon-book"></i>
            <h6 class="d-inline-block ms-1 mb-0">No. of Sales Issued</h6>
         </div>
         <span class="py-1 px-2 rounded-pill bg-white">2</span>
      </article>
   </div>

   <div class="col-md-6">
      <article class="alert-warning p-3 rounded mb-4 shadow-sm d-flex align-items-center justify-content-between">
         <div>      
            <i class="feather icon-book"></i>
            <h6 class="d-inline-block ms-1 mb-0">No. of Purchase Issued</h6>
         </div>
         <span class="py-1 px-2 rounded-pill bg-white">4</span>
      </article>
   </div>

</section>

<?php echo $__env->make('widgets.material', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row">
   <div class="col-xl-6">
      <?php echo $__env->make('widgets.sale-chart', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
   </div>
   <div class="col-xl-6">
      <?php echo $__env->make('widgets.purchase-chart', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
   </div>
</div>



<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\User::class)): ?>
  <?php echo $__env->make('widgets.visit-chart', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php echo $__env->make('widgets.visit-summary', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>

<div class="row">
   <div class="col-xl-6">
      <?php echo $__env->make('widgets.task', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
   </div>
   <div class="col-xl-6">
      <?php echo $__env->make('widgets.leave', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
   </div>
</div>

<?php echo $__env->make('widgets.news', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row">
   <div class="col-xl-6">
     <?php echo $__env->make('widgets.customer-occasion', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
   </div>
   <div class="col-xl-6">
     <?php echo $__env->make('widgets.employee-occasion', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
   </div>
</div>



<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/dashboard/show.blade.php ENDPATH**/ ?>