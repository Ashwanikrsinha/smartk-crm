
<?php $__env->startSection('content'); ?>

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Suppliers</h5>
  
  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Supplier::class)): ?> 
  <div class="d-flex align-items-center">
    <div class="dropdown shadow-sm me-2">
      <button class="btn bg-white dropdown-toggle" type="button" id="settings-dropdown" data-bs-toggle="dropdown">
        <i class="feather icon-settings"></i>  
      </button>
  
      <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="settings-dropdown">
          <a href="<?php echo e(route('supplier-types.index')); ?>" class="dropdown-item">Supplier Types</a>
          <a href="<?php echo e(route('segments.index')); ?>" class="dropdown-item">Segments</a>
          <a href="<?php echo e(route('designations.index')); ?>" class="dropdown-item">Designations</a>
      </div>
    </div>
  
    <a href="<?php echo e(route('suppliers.create')); ?>" class="btn btn-primary">Supplier</a>
  </div>
  <?php endif; ?>
</header>

<?php
  $columns = ['id', 'name', 'segment', 'phone no', 'state' ,'actions'];
?>

<?php if (isset($component)) { $__componentOriginal8aaf9779783cdf64609094123653a0b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8aaf9779783cdf64609094123653a0b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.datatable','data' => ['id' => 'suppliers','columns' => $columns]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('datatable'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'suppliers','columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columns)]); ?>
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

        $('table#suppliers').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: `<?php echo e(route('suppliers.index')); ?>`,
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'segment.name', name: 'segment.name', sortable : false },
                { data: 'phone_number', name: 'phone_number' },
                { data: 'state', name: 'state'},
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/suppliers/index.blade.php ENDPATH**/ ?>