<?php $__env->startSection('content'); ?>

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Products</h5>

  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Product::class)): ?>
    <div class="d-flex align-items-center">

      <div class="dropdown shadow-sm me-2">
        <button class="btn bg-white dropdown-toggle" type="button" id="settings-dropdown" data-bs-toggle="dropdown">
          <i class="feather icon-settings"></i>
        </button>

        <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="settings-dropdown">
            <a href="<?php echo e(route('units.index')); ?>" class="dropdown-item">Units</a>
            
            <a href="<?php echo e(route('categories.index')); ?>" class="dropdown-item">Categories</a>
        </div>
      </div>

      <a href="<?php echo e(route('products.create')); ?>" class="btn btn-primary">Product</a>

    </div>
  <?php endif; ?>

</header>

<?php
  $columns = ['id', 'name', 'HSN code', 'unit',  'category',  're order level', 'actions'];
?>

<?php if (isset($component)) { $__componentOriginal8aaf9779783cdf64609094123653a0b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8aaf9779783cdf64609094123653a0b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.datatable','data' => ['id' => 'products','columns' => $columns]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('datatable'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'products','columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columns)]); ?>
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

        $('table#products').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '<?php echo e(route('products.index')); ?>',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'code', name: 'code' },
                { data: 'unit.name', name: 'unit.name' },
                // { data: 'group.name', name: 'group.name' },
                { data: 'category.name', name: 'category.name' },
                { data: 'reorder_level', name: 'reorder_level' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/products/index.blade.php ENDPATH**/ ?>