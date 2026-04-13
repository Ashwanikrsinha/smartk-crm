<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $role)): ?>
<a href="<?php echo e(route('roles.edit', ['role' => $role])); ?>" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $role)): ?>
<form class="d-inline-block" action="<?php echo e(route('roles.destroy', ['role' => $role])); ?>" method="POST"
 onsubmit="return confirm('Are You Sure?')">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
<?php endif; ?><?php /**PATH D:\Data\smartk-crm\resources\views/roles/buttons.blade.php ENDPATH**/ ?>