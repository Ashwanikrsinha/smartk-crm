
<?php $__env->startSection('content'); ?>

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Items</h5>
</header>

<?php
  $columns = [
    'id', 
    'company name', 
    'item name', 
    'item id', 
    'master id',
    'unit', 
    'part number', 
    'group', 
    'item alias',
    'category', 
    'hsn code',
    'tax rate'
    ];
?>

<?php if (isset($component)) { $__componentOriginal8aaf9779783cdf64609094123653a0b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8aaf9779783cdf64609094123653a0b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.datatable','data' => ['id' => 'items','columns' => $columns,'small' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('datatable'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'items','columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columns),'small' => true]); ?>
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

        $('table#items').DataTable({
             processing: true,
             serverSide: true,
             order: [[0,'desc']],
             ajax: {
              url : '<?php echo e(route('items.index')); ?>',
              beforeSend: function (xhr){ xhr.setRequestHeader('Authorization', '<?php echo e($bearer_token); ?>'); }
            },
             columns: [
                { data: 'id', visible: false, searchable: false },
                { data: 'company_name', name: 'company_name' },
                { data: 'name', name: 'name' },
                { data: 'item_id', name: 'item_id' },
                { data: 'master_id', name: 'master_id' },
                { data: 'unit', name: 'unit' },
                { data: 'part_number', name: 'part_number' },
                { data: 'group', name: 'group' },
                { data: 'item_alias', name: 'item_alias' },
                { data: 'category', name: 'category' },
                { data: 'hsn_code', name: 'hsn_code' },
                { data: 'tax_rate', name: 'tax_rate' },
            ],
        });
    });   
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/tally/items.blade.php ENDPATH**/ ?>