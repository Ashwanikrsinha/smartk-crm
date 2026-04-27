<?php echo csrf_field(); ?>

<div class="row">


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" value="<?php echo e($product->name ?? old('name')); ?>">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">HSN Code</label>
        <input type="tel" class="form-control" name="code" value="<?php echo e($product->code ?? old('code')); ?>">
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Unit</label>
        <select name="unit_id" id="" class="form-control" required>
            <option selected value="">Choose...</option>
            <?php if(isset($product)): ?>
                <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($id); ?>" <?php echo e($product->unit_id == $id ? 'selected': ''); ?>><?php echo e($unit); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($id); ?>"><?php echo e($unit); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>

    

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Category</label>
        <select name="category_id" id="" class="form-control" required>
            <option selected value="">Choose...</option>
            <?php if(isset($product)): ?>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($id); ?>" <?php echo e($product->category_id == $id ? 'selected': ''); ?>><?php echo e($category); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($id); ?>"><?php echo e($category); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>


    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Price</label>
        <input type="number" class="form-control" name="price" value="<?php echo e($product->price ?? old('price')); ?>" required>
    </div>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Re Order Level</label>
        <input type="number" class="form-control" name="reorder_level" value="<?php echo e($product->reorder_level ?? old('reorder_level')); ?>" required>
    </div>

</div>



<div id="filepond-alert" class="alert alert-danger d-none my-3">
Only images: jpg, png, jpeg files are allowed with max size 10MB.
</div>

<div class="mb-3">
    <label for="images" class="form-label">Images</label>
    <input type="file" name="images[]" multiple max="3" id="images">
</div>


<div class="mb-3">
    <label for="" class="form-label">Description</label>
    <textarea name="description" cols="30" rows="5" class="form-control"><?php echo e($product->description ?? old('description')); ?></textarea>
</div>



<button type="submit" class="btn btn-primary"><?php echo e($mode == 'create' ? 'Save' : 'Edit'); ?></button>

<?php $__env->startPush('scripts'); ?>

<script>


    $(document).ready(() => {

        $('select').selectize();

        const filePondAlertEl = $('#filepond-alert');

        FilePond.create(document.querySelector('#images'));

        FilePond.setOptions({
            server : {
                headers : {
                      'X-CSRF-TOKEN' : '<?php echo e(csrf_token()); ?>',
                      'X-Requested-With': 'XMLHttpRequest',
                },
                process : {
                    url : `<?php echo e(route('products.images.store')); ?>`,
                    onerror : (res) => {
                        console.log(res);
                        filePondAlertEl.removeClass('d-none');
                        setTimeout(() => filePondAlertEl.addClass('d-none'), 4000);
                    }
                },
                revert: {
                  url : `<?php echo e(route('products.images.destroy')); ?>`,
                  _method : 'DELETE',
                }
            }
        })



    });

</script>

<?php $__env->stopPush(); ?>
<?php /**PATH D:\Data\smartk-crm\resources\views/products/form.blade.php ENDPATH**/ ?>