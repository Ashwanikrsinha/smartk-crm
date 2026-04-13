<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', $target)): ?>
<a href="<?php echo e(route('targets.show', ['target' => $target])); ?>" class="btn btn-sm text-primary"><i class="feather icon-eye"></i></a>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $target)): ?>
<a href="<?php echo e(route('targets.edit', ['target' => $target])); ?>" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $target)): ?>
<form class="d-inline-block" action="<?php echo e(route('targets.destroy', ['target' => $target])); ?>" method="POST"
onsubmit="return confirm('Are You Sure?')">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
<?php endif; ?><?php /**PATH D:\Data\smartk-crm\resources\views/targets/buttons.blade.php ENDPATH**/ ?>