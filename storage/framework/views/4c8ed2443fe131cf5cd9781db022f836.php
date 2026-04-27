<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['id', 'columns', 'small']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['id', 'columns', 'small']); ?>
<?php foreach (array_filter((['id', 'columns', 'small']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="card border-0 shadow-sm rounded mb-4">
    <div class="card-body px-0">
        <section class="table-responsive-lg">
            <table class="table w-100 <?php echo e(isset($small) ? 'small' : ''); ?>" id="<?php echo e($id ?? ''); ?>">
                <thead>
                <tr>
                <?php if(isset($columns)): ?>
                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th><?php echo e(ucwords($column)); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
                </tr>
                </thead>
            </table>
        </section>
    </div>
</div>
<?php /**PATH D:\Data\smartk-crm\resources\views/components/datatable.blade.php ENDPATH**/ ?>