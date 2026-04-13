<section class="table-responsive-lg rounded mt-4">
    <table class="table table-bordered" id="contact" style="min-width: 50rem;">
        <thead>
            <tr>
                <th colspan="5" class="text-center">
                    <i class="feather icon-users me-1"></i> Contact Persons
                </th>
            </tr>
            <tr>
                <th>Person Name</th>
                <th>Birth Date</th>
                <th>Anniversary Date</th>
                <th>Contact No.</th>
                <th>Designation</th>
            </tr>
        </thead>
        <tbody>
           <?php if(isset($customer) && $customer->contacts->count() > 0): ?>
            <?php $__currentLoopData = $customer->contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td>
                    <input type="text" name="person_name[]" value="<?php echo e($contact->name); ?>" class="form-control">
                </td>
                <td>
                    <input type="date" name="birth_date[]"
                        value="<?php echo e(isset($contact->birth_date) ? $contact->birth_date->format('Y-m-d') : ''); ?>"
                        class="form-control">
                </td>
                <td>
                    <input type="date" name="marriage_date[]"
                        value="<?php echo e(isset($contact->marriage_date) ? $contact->marriage_date->format('Y-m-d') : ''); ?>"
                        class="form-control">
                </td>
                <td>
                    <input type="text" name="contact_number[]" value="<?php echo e($contact->contact_number); ?>"
                        class="form-control">
                </td>
                <td style="min-width: 12rem;">
                    <select name="designation_id[]" id="" class="form-control">
                        <option value="" selected disabled>Choose...</option>
                        <?php $__currentLoopData = $designations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $designation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option <?php echo e($contact->designation_id == $id ? 'selected' : ''); ?> value="<?php echo e($id); ?>">
                        <?php echo e($designation); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
           <?php else: ?>
            <tr>
                <td>
                    <input type="text" name="person_name[]" class="form-control">
                </td>
                <td>
                    <input type="date" name="birth_date[]" class="form-control">
                </td>
                <td>
                    <input type="date" name="marriage_date[]" class="form-control">
                </td>
                <td>
                    <input type="text" name="contact_number[]" class="form-control">
                </td>
                <td style="min-width: 12rem;">
                    <select name="designation_id[]" id="" class="form-control">
                        <option value="" selected>Choose...</option>
                        <?php $__currentLoopData = $designations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $designation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($id); ?>"><?php echo e($designation); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </td>
            </tr>
           <?php endif; ?>
        </tbody>
    </table>
</section>

<footer class="d-flex justify-content-between mt-3 mt-lg-0 mb-4">
    <button class="btn btn-sm btn-primary" id="add-row">
        <span class="feather icon-plus"></span>
    </button>
    <button class="btn btn-sm btn-danger" id="remove-row">
        <i class="feather icon-x"></i>
    </button>
</footer>
<?php /**PATH D:\Data\smartk-crm\resources\views/customers/contacts.blade.php ENDPATH**/ ?>