<?php $__env->startSection('content'); ?>
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Schools</h5>
            <small class="text-muted">All registered schools</small>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Customer::class)): ?>
            <a href="<?php echo e(route('customers.create')); ?>" class="btn btn-primary btn-sm">
                <i class="feather icon-plus me-1"></i> New School
            </a>
        <?php endif; ?>
    </header>

    <div class="bg-white rounded shadow-sm p-3">
        <?php if (isset($component)) { $__componentOriginal8aaf9779783cdf64609094123653a0b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8aaf9779783cdf64609094123653a0b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.datatable','data' => ['id' => 'schools','columns' => ['Code', 'School Name', 'City', 'State', 'Phone', 'Lead Source', 'SP', 'POs', 'Actions']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('datatable'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'schools','columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Code', 'School Name', 'City', 'State', 'Phone', 'Lead Source', 'SP', 'POs', 'Actions'])]); ?>
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
        $(document).ready(function() {
            $('table#schools').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, 'desc']
                ],
                ajax: '<?php echo e(route('customers.index')); ?>',
                columns: [{
                        data: 'school_code',
                        name: 'school_code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    {
                        data: 'state',
                        name: 'state'
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number',
                        searchable: false
                    },
                    {
                        data: 'lead_source',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_by',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'po_count',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/customers/index.blade.php ENDPATH**/ ?>