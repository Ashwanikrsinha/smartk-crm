<?php $__env->startSection('content'); ?>
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Purchase Orders</h5>
            <small class="text-muted">Manage all sales orders</small>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Invoice::class)): ?>
            <a href="<?php echo e(route('invoices.create')); ?>" class="btn btn-primary btn-sm">
                <i class="feather icon-plus me-1"></i> New PO
            </a>
        <?php endif; ?>
    </header>

    <?php
        $columns = [
            'po_number',
            'date',
            'school',
            'sales_person',
            'PO Amount',
            'Billed',
            'Collected',
            'Outstanding',
            'status',
            'actions',
        ];
    ?>

    <div class="bg-white rounded shadow-sm p-3">
        <?php if (isset($component)) { $__componentOriginal8aaf9779783cdf64609094123653a0b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8aaf9779783cdf64609094123653a0b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.datatable','data' => ['id' => 'invoices','columns' => $columns]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('datatable'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'invoices','columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columns)]); ?>
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

            $('table#invoices').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, 'desc']
                ],
                ajax: '<?php echo e(route('invoices.index')); ?>',
                columns: [{
                        data: 'po_number',
                        name: 'po_number'
                    },
                    {
                        data: 'invoice_date',
                        name: 'invoice_date'
                    },
                    {
                        data: 'customer.name',
                        name: 'customer.name'
                    },
                    {
                        data: 'user.username',
                        name: 'user.username'
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        searchable: false
                    },
                    {
                        data: 'billing_amount',
                        name: 'billing_amount',
                        searchable: false
                    },
                    {
                        data: 'collected_amount',
                        name: 'collected_amount',
                        searchable: false
                    },
                    {
                        data: 'outstanding_amount',
                        name: 'outstanding_amount',
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
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

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/invoices/index.blade.php ENDPATH**/ ?>