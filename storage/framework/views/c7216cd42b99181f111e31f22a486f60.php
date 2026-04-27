<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', $customer)): ?>
    <a href="<?php echo e(route('customers.show', $customer)); ?>" class="btn btn-sm text-primary" title="View School">
        <i class="feather icon-eye"></i>
    </a>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $customer)): ?>
    <a href="<?php echo e(route('customers.edit', $customer)); ?>" class="btn btn-sm text-primary" title="Edit School">
        <i class="feather icon-edit-2"></i>
    </a>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Invoice::class)): ?>
    <a href="<?php echo e(route('invoices.create')); ?>?customer_id=<?php echo e($customer->id); ?>" class="btn btn-sm text-success"
        title="New PO for this school">
        <i class="feather icon-file-plus"></i>
    </a>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $customer)): ?>
    <form class="d-inline-block" action="<?php echo e(route('customers.destroy', $customer)); ?>" method="POST"
        onsubmit="return confirm('Delete <?php echo e(addslashes($customer->name)); ?>? This cannot be undone.')">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        <button class="btn btn-sm text-danger" title="Delete School">
            <i class="feather icon-trash-2"></i>
        </button>
    </form>
<?php endif; ?>
<?php /**PATH D:\Data\smartk-crm\resources\views/customers/buttons.blade.php ENDPATH**/ ?>