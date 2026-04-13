<section class="table-responsive-lg rounded mt-3">
    <table class="table table-bordered rounded" id="item" style="min-width: 50rem;">
        <thead class="small">
            <tr>
                <th>Product Name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($invoice) && $invoice->invoiceItems->count() > 0): ?>
                <?php $__currentLoopData = $invoice->invoiceItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoiceItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="min-width: 18rem;">
                        <select name="products[]" class="form-control" required>
                            <option value="" selected>Choose...</option>
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e($key==$invoiceItem->product_id ? 'selected' : ''); ?>>
                                <?php echo e($product); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="descriptions[]" min="0" value="<?php echo e($invoiceItem->description); ?>" maxlength="150"
                            class="form-control">
                    </td>
                    <td>
                        <input type="number" name="quantities[]" min="0" value="<?php echo e($invoiceItem->quantity); ?>" step=".01"
                            class="form-control" required>
                    </td>
                    <td style="min-width: 10rem;">
                        <select name="units[]" class="form-control" required>
                            <option value="" selected>Choose...</option>
                            <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e($key==$invoiceItem->unit_id ? 'selected' : ''); ?> ><?php echo e($unit); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>
                    <td>
                        <input type="number" name="rates[]" min="0" value="<?php echo e($invoiceItem->rate); ?>" step=".0001" 
                        class="form-control" required>
                    </td>
                    <td>
                        <input type="number" name="amounts[]" min="0" value="<?php echo e($invoiceItem->amount); ?>" step=".0001" 
                        class="form-control" required>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <tr>
                <td style="min-width: 16rem;">
                    <select name="products[]" class="form-control" required>
                        <option value="" selected>Choose...</option>
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>"><?php echo e($product); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </td>
                <td>
                    <input type="text" name="descriptions[]" class="form-control" maxlength="150">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="quantities[]" class="form-control" required>
                </td>
                <td style="min-width: 10rem;">
                    <select name="units[]" class="form-control" required>
                        <option value="" selected>Choose...</option>
                        <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>"><?php echo e($unit); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </td>
                <td>
                    <input type="number" name="rates[]" min="0" step=".0001" class="form-control" required>
                </td>
                <td>
                    <input type="number" name="amounts[]" min="0" step=".0001" class="form-control" required>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"></td>
                <td colspan="2">
                    <div class="input-group">
                        <span class="input-group-text small">Amount</span>
                        <input type="number" name="amount" step="0.0001" value="<?php echo e($invoice->amount ?? 0); ?>"  
                        class="form-control" readonly required>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>

</section>

<?php /**PATH D:\Data\smartk-crm\resources\views/invoices/items.blade.php ENDPATH**/ ?>