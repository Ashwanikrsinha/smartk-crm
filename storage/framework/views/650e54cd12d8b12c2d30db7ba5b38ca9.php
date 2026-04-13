
<?php $__env->startSection('content'); ?>

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Visits</h5>

  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Visit::class)): ?>


    <div class="d-flex align-items-center">

      <div class="d-flex align-items-center">

        <div class="dropdown shadow-sm me-2">
        <button class="btn bg-white dropdown-toggle" type="button" id="settings-dropdown" data-bs-toggle="dropdown">
          <i class="feather icon-settings"></i>  
        </button>
    
        <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="settings-dropdown">
            <a href="<?php echo e(route('purposes.index')); ?>" class="dropdown-item">Purposes</a>
        </div>
      </div>
    

      <div class="d-flex align-items-center">

        <div class="input-group me-2">
          <span class="input-group-text bg-white">
            <i class="feather icon-calendar"></i>
          </span>
          <input type="date" class="form-control" id="visit-date">
        </div>
        
        <a href="<?php echo e(route('visits.create')); ?>" class="btn btn-primary">Visit</a>
        
      </div>
  
    </div>

  <?php endif; ?>
</header>

<?php echo $__env->make('visits.accordion', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php
  $columns = ['id', 'visit no', 'visit date', 'username', 'customer', 'purpose', 'level', 'status', 'actions'];
?>

<?php if (isset($component)) { $__componentOriginal8aaf9779783cdf64609094123653a0b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8aaf9779783cdf64609094123653a0b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.datatable','data' => ['id' => 'visits','columns' => $columns,'small' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('datatable'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'visits','columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columns),'small' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8aaf9779783cdf64609094123653a0b9)): ?>
<?php $attributes = $__attributesOriginal8aaf9779783cdf64609094123653a0b9; ?>
<?php unset($__attributesOriginal8aaf9779783cdf64609094123653a0b9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8aaf9779783cdf64609094123653a0b9)): ?>
<?php $component = $__componentOriginal8aaf9779783cdf64609094123653a0b9; ?>
<?php unset($__componentOriginal8aaf9779783cdf64609094123653a0b9); ?>
<?php endif; ?>


<?php $__env->stopSection(); ?>


<?php $__env->startPush('scripts'); ?>
<script>
  $(document).ready(() => {

        $('input[type=date]').flatpickr({
            altInput: true,
            altFormat: 'F j, Y',
            dateFormat: 'Y-m-d',
        });

      let table =  $('table#visits').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '<?php echo e(route("visits.index")); ?>',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'visit_number', name: 'visit_number' },
                { data: 'visit_date', name: 'visit_date' },
                { data: 'user.username', name: 'user.username', sortable : false },
                { data: 'customer.name', name: 'customer.name', sortable : false },
                { data: 'purpose.name', name: 'purpose.name', sortable : false },
                { data: 'level', name: 'level', sortable : false },
                { data: 'status', name: 'status', sortable : false },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });

        $('#visit-date').change(function(){
          table.column(2).search($(this).val()).draw();
      });
      
    });   

</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/visits/index.blade.php ENDPATH**/ ?>