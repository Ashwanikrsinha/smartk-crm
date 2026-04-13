
<?php $__env->startSection('content'); ?>

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Visit Targets</h5>
  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Target::class)): ?>
   <a href="<?php echo e(route('targets.create')); ?>" class="btn btn-primary">Targets</a>
  <?php endif; ?>
</header>

<?php
  $columns = ['id', 'executive', 'target', 'start date', 'end date', 'complete', 'actions'];
?>

<?php if (isset($component)) { $__componentOriginal8aaf9779783cdf64609094123653a0b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8aaf9779783cdf64609094123653a0b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.datatable','data' => ['id' => 'targets','columns' => $columns]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('datatable'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'targets','columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columns)]); ?>
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

        $('table#targets').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: '<?php echo e(route("targets.index")); ?>',
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'user.username', name: 'user.username' },
                { data: 'target', name: 'target' },
                { data: 'start_date', name: 'start_date' },
                { data: 'end_date', name: 'end_date' },
                { data: 'complete', name: 'complete' },
                { data: 'action', 'orderable': false, searchable: false}
            ],
        });
    });   
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/targets/index.blade.php ENDPATH**/ ?>