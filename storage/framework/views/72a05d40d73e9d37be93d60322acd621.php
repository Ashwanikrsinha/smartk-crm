<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', $user)): ?>
<a href="<?php echo e(route('users.show', ['user' => $user])); ?>" class="btn btn-sm text-primary"><i class="feather icon-eye"></i></a>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $user)): ?>
<a href="<?php echo e(route('users.edit', ['user' => $user])); ?>" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $user)): ?>
<form class="d-inline-block" action="<?php echo e(route('users.destroy', ['user' => $user])); ?>" method="POST"
 onsubmit="return confirm('Are You Sure?')">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
<?php endif; ?><?php /**PATH D:\Data\smartk-crm\resources\views/users/buttons.blade.php ENDPATH**/ ?>