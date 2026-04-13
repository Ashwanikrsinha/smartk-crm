<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', $bill)): ?>

<a href="<?php echo e(route('bills.show', ['bill' => $bill, 'type' => $bill::WITH_PRICE])); ?>" 
    class="btn btn-sm text-primary" title="With Price">
    <i class="feather icon-printer"></i>
</a>

<a href="<?php echo e(route('bills.show', ['bill' => $bill, 'type' => $bill::WITHOUT_PRICE ])); ?>" 
    class="btn btn-sm text-primary" title="Without Price">
    <i class="feather icon-printer"></i>
</a>

<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $bill)): ?>
<a href="<?php echo e(route('bills.edit', ['bill' => $bill])); ?>" class="btn btn-sm text-primary"><i
        class="feather icon-settings"></i></a>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $bill)): ?>
<form class="d-inline-block" action="<?php echo e(route('bills.destroy', ['bill' => $bill])); ?>" method="POST"
    onsubmit="return confirm('Are You Sure?')">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
<?php endif; ?><?php /**PATH D:\Data\smartk-crm\resources\views/bills/buttons.blade.php ENDPATH**/ ?>