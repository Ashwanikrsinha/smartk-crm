<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', $visit)): ?>
<a href="<?php echo e(route('visits.show', ['visit' => $visit])); ?>" class="btn btn-sm text-primary"><i class="feather icon-eye"></i></a>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $visit)): ?>
<a href="<?php echo e(route('visits.edit', ['visit' => $visit])); ?>" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $visit)): ?>
<form class="d-inline-block" action="<?php echo e(route('visits.destroy', ['visit' => $visit])); ?>" method="POST"
onsubmit="return confirm('Are You Sure?')">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
<?php endif; ?>
<?php /**PATH D:\Data\smartk-crm\resources\views/visits/buttons.blade.php ENDPATH**/ ?>