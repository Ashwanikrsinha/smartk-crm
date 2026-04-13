<section class="table-responsive-lg rounded mt-3">
    <table class="table table-bordered rounded" id="item" style="min-width: 50rem;">
        <thead>
            <tr class="align-middle">
                <th colspan="5">
                    <input type="checkbox" id="check-items" class="form-check-input"
                    <?php echo e(isset($visit) && $visit->visitItems->count() > 0 ? 'checked' : ''); ?>>
                    <span class="ms-2"> Visit <?php echo e(isset($visit) ? "No. {$visit->visit_number}" : ''); ?> Items</span>
                </th>
            </tr>
            <tr>
                <th>Product Name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Rate</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($visit) && $visit->visitItems->count() > 0): ?>
                <?php $__currentLoopData = $visit->visitItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visitItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="min-width: 18rem;">
                        <select name="products[]" class="form-control" required>
                            <option value="" selected>Choose...</option>
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e($key==$visitItem->product_id ? 'selected' : ''); ?>>
                                <?php echo e($product); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="descriptions[]" value="<?php echo e($visitItem->description); ?>" maxlength="150"
                            class="form-control">
                    </td>
                    <td>
                        <input type="number" name="quantities[]" value="<?php echo e($visitItem->quantity); ?>" step=".01"
                            class="form-control" required>
                    </td>
                    <td style="min-width: 12rem;">
                        <select name="units[]" class="form-control" required>
                            <option value="" selected>Choose...</option>
                            <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e($key==$visitItem->unit_id ? 'selected' : ''); ?> ><?php echo e($unit); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>
                    <td>
                        <input type="number" name="rates[]" value="<?php echo e($visitItem->rate); ?>" step=".0001" 
                        class="form-control" required>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <tr>
                <td style="min-width: 16rem;">
                    <select name="products[]" class="form-control" disabled>
                        <option value="" selected>Choose...</option>
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>"><?php echo e($product); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </td>
                <td>
                    <input type="text" name="descriptions[]" class="form-control" maxlength="150" disabled>
                </td>
                <td>
                    <input type="number" step="0.01" name="quantities[]" class="form-control" disabled>
                </td>
                <td style="min-width: 12rem;">
                    <select name="units[]" class="form-control" disabled>
                        <option value="" selected>Choose...</option>
                        <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>"><?php echo e($unit); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </td>
                <td>
                    <input type="number" name="rates[]" step=".0001" class="form-control" disabled>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>


<footer class="d-flex justify-content-between mt-3 mt-lg-0 mb-4">
    <button class="btn btn-primary btn-sm" id="add-row">
        <span class="feather icon-plus"></span>
    </button>
    <button class="btn btn-danger btn-sm" id="remove-row">
        <i class="feather icon-x"></i>
    </button>
</footer>

<?php /**PATH D:\Data\smartk-crm\resources\views/visits/items.blade.php ENDPATH**/ ?>