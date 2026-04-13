
<?php $__env->startSection('content'); ?>

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Ledgers</h5>
</header>

<?php
  $columns = [
    'id', 
    'company name', 
    'ledger name', 
    'ledger id', 
    'master id',  
    'group', 
    'state', 
    'gst type'
    ];
?>

<?php if (isset($component)) { $__componentOriginal8aaf9779783cdf64609094123653a0b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8aaf9779783cdf64609094123653a0b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.datatable','data' => ['id' => 'ledgers','columns' => $columns,'small' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('datatable'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'ledgers','columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columns),'small' => true]); ?>
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

        $('table#ledgers').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: {
              url : '<?php echo e(route('ledgers.index')); ?>',
              beforeSend: function (xhr){ xhr.setRequestHeader('Authorization', '<?php echo e($bearer_token); ?>'); }
            },
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'company_name', name: 'company_name' },
                { data: 'ledger_name', name: 'ledger_name' },
                { data: 'ledger_id', name: 'ledger_id' },
                { data: 'master_id', name: 'master_id' },
                { data: 'ledger_group', name: 'ledger_group' },
                { data: 'state', name: 'state' },
                { data: 'gst_type', name: 'gst_type' },
            ],
        });
    });   
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/tally/ledgers.blade.php ENDPATH**/ ?>