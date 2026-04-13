<?php echo csrf_field(); ?>
<div class="row">
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" value="<?php echo e($customer->name ?? old('name')); ?>" required>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Customer Type</label>
        <select name="customer_types[]" class="form-control" multiple>
            <option value="" selected disabled>Choose...</option>
            <?php if(isset($customer)): ?>
                <?php $__currentLoopData = $customer_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option <?php echo e(in_array($key, explode(',', $customer->customer_types)) ? 'selected' : ''); ?>

                         value="<?php echo e($key); ?>">
                        <?php echo e($type); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <?php $__currentLoopData = $customer_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>"><?php echo e($type); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Segment</label>
        <select name="segment_id" id="segment_id" class="form-control">
            <option selected value="">Choose...</option>
            <?php if(isset($customer)): ?>
                <?php $__currentLoopData = $segments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option <?php echo e($customer->segment_id == $key ? 'selected' : ''); ?> value="<?php echo e($key); ?>"><?php echo e($segment); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <?php $__currentLoopData = $segments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option <?php echo e(old('segment_id') == $key ? 'selected' : ''); ?> value="<?php echo e($key); ?>"><?php echo e($segment); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">State</label>
        <select name="state" class="form-control">
            <option selected value="">Choose...</option>
            <?php if(isset($customer)): ?>
                <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($state); ?>" <?php echo e($customer->state == $state ? 'selected' : ''); ?>><?php echo e($state); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>  
            <?php else: ?>
                <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($state); ?>"><?php echo e($state); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">City</label>
        <input type="text" class="form-control" name="city" value="<?php echo e($customer->city ?? old('city')); ?>">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Address</label>
        <input type="text" class="form-control" name="address" value="<?php echo e($customer->address ?? old('address')); ?>">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">GST No.</label>
        <input type="text" class="form-control" name="gst_number" value="<?php echo e($customer->gst_number ?? old('gst_number')); ?>">
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Email</label>
        <input type="text" class="form-control" name="email" value="<?php echo e($customer->email ?? old('email')); ?>">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Phone No.</label>
        <input type="text" class="form-control" name="phone_number" value="<?php echo e($customer->phone_number ?? old('phone_number')); ?>">
    </div>



    <?php echo $__env->make('customers.contacts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="mb-3">
        <label for="" class="form-label">Description</label>
        <textarea name="description" cols="30" rows="5" class="form-control"><?php echo e($customer->description ?? old('description')); ?></textarea>
    </div>

</div>

<button type="submit" class="btn btn-primary"><?php echo e($mode == 'create' ? 'Save' : 'Edit'); ?></button>

<?php $__env->startPush('scripts'); ?>

<script>
    function enableSelectize() {
        $('table#contact tbody').find('select').selectize({
            sortField: 'text'
        });
    }

    $(document).ready(() => {

       $('select').selectize();
        

        $('#add-row').click(function (e) {

            e.preventDefault();

            $('table#contact tbody tr:last').find('select').each(function (el) {
                let value = $(this).val();
                $(this)[0].selectize.destroy();
                $(this).val(value);
            });

            $('table#contact tbody tr:last').clone()
                .appendTo('table#contact tbody')
                .find('[name]').val('');

            enableSelectize();
        });


        // remove last row
        $('#remove-row').on('click', (e) => {
            e.preventDefault();
            $('table#contact tbody tr:last').remove();
        });


    });

</script>

<?php $__env->stopPush(); ?>
<?php /**PATH D:\Data\smartk-crm\resources\views/customers/form.blade.php ENDPATH**/ ?>