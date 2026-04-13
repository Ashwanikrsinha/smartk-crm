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
            <?php if(isset($bill) && $bill->billItems->count() > 0): ?>
                <?php $__currentLoopData = $bill->billItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $billItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="min-width: 18rem;">
                        <select name="products[]" class="form-control" required>
                            <option value="" selected>Choose...</option>
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e($key==$billItem->product_id ? 'selected' : ''); ?>>
                                <?php echo e($product); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="descriptions[]" min="0" value="<?php echo e($billItem->description); ?>" maxlength="150"
                            class="form-control">
                    </td>
                    <td>
                        <input type="number" name="quantities[]" min="0" value="<?php echo e($billItem->quantity); ?>" step=".01"
                            class="form-control" required>
                    </td>
                    <td style="min-width: 10rem;">
                        <select name="units[]" class="form-control" required>
                            <option value="" selected>Choose...</option>
                            <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e($key==$billItem->unit_id ? 'selected' : ''); ?> ><?php echo e($unit); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>
                    <td>
                        <input type="number" name="rates[]" min="0" value="<?php echo e($billItem->rate); ?>" step=".0001" 
                        class="form-control" required>
                    </td>
                    <td>
                        <input type="number" name="amounts[]" min="0" value="<?php echo e($billItem->amount); ?>" step=".0001" 
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
                        <input type="number" name="amount" step="0.0001" value="<?php echo e($bill->amount ?? 0); ?>"  
                        class="form-control" readonly required>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text small">CGST %</span>
                        <input type="number" id="cgst-percentage" class="form-control" 
                        value="<?php echo e(isset($bill) ? round($bill->cgst_amount / $bill->amount * 100)  : ''); ?>"
                        required>
                    </div>
                </td>
                <td colspan="2">
                    <div class="input-group">
                        <span class="input-group-text small">CGST Amount</span>
                        <input type="number" name="cgst_amount" step="0.0001" value="<?php echo e($bill->cgst_amount ?? 0); ?>" 
                        class="form-control" readonly required>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text small">SGST %</span>
                        <input type="number" id="sgst-percentage" class="form-control"
                        value="<?php echo e(isset($bill) ? round($bill->sgst_amount / $bill->amount * 100)  : ''); ?>" 
                        required>
                    </div>
                </td>
                <td colspan="2">
                    <div class="input-group">
                        <span class="input-group-text small">SGST Amount</span>
                        <input type="number" name="sgst_amount" step="0.0001" value="<?php echo e($bill->sgst_amount ?? 0); ?>"
                        class="form-control" readonly required>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text small">IGST %</span>
                        <input type="number" id="igst-percentage" class="form-control"
                        value="<?php echo e(isset($bill) ? round($bill->igst_amount / $bill->amount * 100)  : ''); ?>" 
                        required>
                    </div>
                </td>
                <td colspan="2">
                    <div class="input-group">
                        <span class="input-group-text small">IGST Amount</span>
                        <input type="number" name="igst_amount" step="0.0001" value="<?php echo e($bill->igst_amount ?? 0); ?>"
                        class="form-control" readonly required>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td colspan="2">
                    <div class="input-group">
                        <span class="input-group-text small">Transport Charges</span>
                        <input type="number" name="transport_charges" id="transport_charges" step="0.0001" value="<?php echo e($bill->transport_charges ?? 0); ?>"
                         class="form-control" value="0" required>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td colspan="2">
                    <div class="input-group">
                        <span class="input-group-text small">Extra Charges</span>
                        <input type="number" name="extra_charges" id="extra_charges" step="0.0001" value="<?php echo e($bill->extra_charges ?? 0); ?>"
                         class="form-control" required>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td colspan="2">
                    <div class="input-group">
                        <span class="input-group-text small">Total Amount</span>
                        <input type="number" name="total_amount" step="0.0001" value="<?php echo e($bill->total_amount ?? 0); ?>"
                         class="form-control" readonly required>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>

</section>

<?php /**PATH D:\Data\smartk-crm\resources\views/bills/items.blade.php ENDPATH**/ ?>