<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $product)): ?>
<a href="<?php echo e(route('products.edit', ['product' => $product])); ?>" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $product)): ?>
<form class="d-inline-block" action="<?php echo e(route('products.destroy', ['product' => $product])); ?>" method="POST"
onsubmit="return confirm('Are You Sure?')">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
<?php endif; ?><?php /**PATH D:\Data\smartk-crm\resources\views/products/buttons.blade.php ENDPATH**/ ?>