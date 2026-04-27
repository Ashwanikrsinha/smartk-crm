<?php $__env->startSection('content'); ?>

<header class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Leaves</h5>
    <?php if(auth()->user()->isSalesPerson()): ?>
    <a href="<?php echo e(route('leaves.create')); ?>" class="btn btn-primary btn-sm">
        <i class="feather icon-plus me-1"></i> Apply Leave
    </a>
    <?php endif; ?>
</header>


<?php if(auth()->user()->isSalesPerson() && isset($leaveBalance)): ?>
<div class="row g-3 mb-4">
    <?php $__currentLoopData = $leaveBalance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $bal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-6 col-lg-3">
        <div class="bg-white rounded shadow-sm p-3 text-center">
            <h6 class="text-muted text-uppercase small mb-2"><?php echo e(ucfirst($type)); ?> Leave</h6>
            <div class="d-flex justify-content-center align-items-end gap-1 mb-1">
                <h4 class="fw-bold mb-0 text-<?php echo e($bal['remaining'] > 2 ? 'success' : 'danger'); ?>">
                    <?php echo e($bal['remaining']); ?>

                </h4>
                <small class="text-muted mb-1">/ <?php echo e($bal['allowed']); ?></small>
            </div>
            <div class="progress" style="height: 5px;">
                <?php $pct = $bal['allowed'] > 0 ? round(($bal['used'] / $bal['allowed']) * 100) : 0; ?>
                <div class="progress-bar bg-<?php echo e($bal['remaining'] > 2 ? 'success' : 'danger'); ?>"
                     style="width: <?php echo e($pct); ?>%"></div>
            </div>
            <small class="text-muted"><?php echo e($bal['used']); ?> used</small>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>


<div class="bg-white rounded shadow-sm p-3">
    <?php if (isset($component)) { $__componentOriginal8aaf9779783cdf64609094123653a0b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8aaf9779783cdf64609094123653a0b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.datatable','data' => ['id' => 'leaves','columns' => ['Employee','Type','From','To','Days','Status','Actions']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('datatable'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'leaves','columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Employee','Type','From','To','Days','Status','Actions'])]); ?>
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
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function () {
    $('table#leaves').DataTable({
        processing:  true,
        serverSide:  true,
        order:       [[0, 'desc']],
        ajax:        '<?php echo e(route('leaves.index')); ?>',
        columns: [
            { data: 'user.username', name: 'user.username' },
            { data: 'leave_type',   name: 'leave_type' },
            { data: 'from_date',   name: 'from_date' },
            { data: 'to_date',     name: 'to_date' },
            { data: 'days',         orderable: false, searchable: false },
            { data: 'status',       name: 'status',   searchable: false },
            { data: 'action',       orderable: false, searchable: false },
        ],
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/leaves/index.blade.php ENDPATH**/ ?>