<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', $supplier)): ?>
<a href="<?php echo e(route('suppliers.show', ['supplier' => $supplier])); ?>" class="btn btn-sm text-primary"><i class="feather icon-eye"></i></a>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $supplier)): ?>
<a href="<?php echo e(route('suppliers.edit', ['supplier' => $supplier])); ?>" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $supplier)): ?>
<form class="d-inline-block" action="<?php echo e(route('suppliers.destroy', ['supplier' => $supplier])); ?>" method="POST"
onsubmit="return confirm('Are You Sure?')">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
<?php endif; ?>
<?php /**PATH D:\Data\smartk-crm\resources\views/suppliers/buttons.blade.php ENDPATH**/ ?>