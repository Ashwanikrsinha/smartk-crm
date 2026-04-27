<?php echo csrf_field(); ?>


<h6 class="fw-bold border-bottom pb-2 mb-3">
    <i class="feather icon-home me-2 text-primary"></i> School Information
</h6>

<div class="row">

    <div class="col-lg-5 mb-3">
        <label class="form-label">School Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="<?php echo e($customer->name ?? old('name')); ?>"
            placeholder="Full school name" required>
    </div>

    <div class="col-lg-4 mb-3">
        <label class="form-label">Lead From</label>
        <select name="lead_source_id" class="form-control">
            <option value="">Select source...</option>
            <?php $__currentLoopData = $lead_sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($id); ?>"
                    <?php echo e((isset($customer) && $customer->lead_source_id == $id) || old('lead_source_id') == $id ? 'selected' : ''); ?>>
                    <?php echo e($name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="col-lg-3 mb-3">
        <label class="form-label">Segment</label>
        <select name="segment_id" class="form-control">
            <option value="">Select segment...</option>
            <?php $__currentLoopData = $segments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($id); ?>"
                    <?php echo e((isset($customer) && $customer->segment_id == $id) || old('segment_id') == $id ? 'selected' : ''); ?>>
                    <?php echo e($name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="col-lg-4 mb-3">
        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
        <input type="text" name="phone_number" class="form-control"
            value="<?php echo e($customer->phone_number ?? old('phone_number')); ?>" placeholder="10-digit mobile" maxlength="15"
            required>
    </div>

    <div class="col-lg-4 mb-3">
        <label class="form-label">Email ID</label>
        <input type="email" name="email" class="form-control" value="<?php echo e($customer->email ?? old('email')); ?>"
            placeholder="school@example.com">
    </div>

    <div class="col-lg-4 mb-3">
        <label class="form-label">GSTIN</label>
        <input type="text" name="gstin" class="form-control" value="<?php echo e($customer->gstin ?? old('gstin')); ?>"
            placeholder="15-character GST number" maxlength="15"
            pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$"
            title="Must be a valid 15-character GSTIN">
        <div class="form-text">Format: 27AAPFU0939F1ZV</div>
    </div>

</div>


<h6 class="fw-bold border-bottom pb-2 mb-3 mt-2">
    <i class="feather icon-map-pin me-2 text-primary"></i> Address Details
</h6>

<div class="row">

    <div class="col-lg-6 mb-3">
        <label class="form-label">School Address</label>
        <input type="text" name="address" class="form-control" value="<?php echo e($customer->address ?? old('address')); ?>"
            placeholder="Street / Area">
    </div>

    <div class="col-lg-3 mb-3">
        <label class="form-label">State <span class="text-danger">*</span></label>
        <input type="text" name="state" class="form-control" value="<?php echo e($customer->state ?? old('state')); ?>"
            list="state-datalist" placeholder="State" required>
        <datalist id="state-datalist">
            <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($st); ?>">
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </datalist>
    </div>

    <div class="col-lg-2 mb-3">
        <label class="form-label">City <span class="text-danger">*</span></label>
        <input type="text" name="city" class="form-control" value="<?php echo e($customer->city ?? old('city')); ?>"
            placeholder="City" required>
    </div>

    <div class="col-lg-1 mb-3">
        <label class="form-label">Pin Code</label>
        <input type="text" name="pin_code" class="form-control" value="<?php echo e($customer->pin_code ?? old('pin_code')); ?>"
            placeholder="6-digit" maxlength="6">
    </div>

    <div class="col-12 mb-3">
        <label class="form-label">Description / Notes</label>
        <textarea name="description" class="form-control" rows="2"
            placeholder="Any additional notes about this school..."><?php echo e($customer->description ?? old('description')); ?></textarea>
    </div>

</div>



<div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-2">
    <h6 class="fw-bold mb-0">
        <i class="feather icon-users me-2 text-primary"></i> Contact Persons
    </h6>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-primary" id="add-contact-row">
            <i class="feather icon-plus"></i> Add
        </button>
        <button type="button" class="btn btn-sm btn-danger" id="remove-contact-row">
            <i class="feather icon-x"></i> Remove
        </button>
    </div>
</div>

<div class="table-responsive rounded mb-4">
    <table class="table table-bordered" id="contact-table" style="min-width: 50rem;">
        <thead class="table-light">
            <tr>
                <th>Person Name</th>
                <th>Birth Date</th>
                <th>Anniversary Date</th>
                <th>Contact No.</th>
                <th>Designation</th>
            </tr>
        </thead>
        <tbody id="contact-tbody">

            <?php if(isset($customer) && $customer->contacts->count() > 0): ?>

                <?php $__currentLoopData = $customer->contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <input type="text" name="person_name[]" value="<?php echo e($contact->name); ?>"
                                class="form-control">
                        </td>
                        <td>
                            <input type="date" name="birth_date[]"
                                value="<?php echo e($contact->birth_date ? $contact->birth_date->format('Y-m-d') : ''); ?>"
                                class="form-control">
                        </td>
                        <td>
                            <input type="date" name="marriage_date[]"
                                value="<?php echo e($contact->marriage_date ? $contact->marriage_date->format('Y-m-d') : ''); ?>"
                                class="form-control">
                        </td>
                        <td>
                            <input type="text" name="contact_number[]" value="<?php echo e($contact->contact_number); ?>"
                                class="form-control">
                        </td>
                        <td style="min-width: 12rem;">
                            <select name="designation_id[]" class="form-control">
                                <option value="">Choose...</option>
                                <?php $__currentLoopData = $designations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $designation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($id); ?>"
                                        <?php echo e($contact->designation_id == $id ? 'selected' : ''); ?>>
                                        <?php echo e($designation); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                
                <tr>
                    <td><input type="text" name="person_name[]" class="form-control" placeholder="Contact name">
                    </td>
                    <td><input type="date" name="birth_date[]" class="form-control"></td>
                    <td><input type="date" name="marriage_date[]" class="form-control"></td>
                    <td><input type="text" name="contact_number[]" class="form-control"
                            placeholder="Phone number"></td>
                    <td style="min-width: 12rem;">
                        <select name="designation_id[]" class="form-control">
                            <option value="">Choose...</option>
                            <?php $__currentLoopData = $designations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $designation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($designation); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>
    </table>
</div>



<?php if($mode === 'create'): ?>
    <h6 class="fw-bold border-bottom pb-2 mb-3 mt-2">
        <i class="feather icon-file me-2 text-primary"></i> Documents
        <small class="text-muted fw-normal">(optional — can be uploaded later)</small>
    </h6>
    <div class="row">
        <div class="col-lg-4 mb-3">
            <label class="form-label">Aadhar Card</label>
            <input type="file" name="aadhar" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            <div class="form-text">PDF or image, max 5MB</div>
        </div>
        <div class="col-lg-4 mb-3">
            <label class="form-label">PAN Card</label>
            <input type="file" name="pan" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        </div>
        <div class="col-lg-4 mb-3">
            <label class="form-label">GST Certificate</label>
            <input type="file" name="gst_certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        </div>
    </div>
<?php endif; ?>



<div class="d-flex gap-2 mt-3 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <i class="feather icon-save me-1"></i>
        <?php echo e($mode === 'create' ? 'Register School' : 'Update School'); ?>

    </button>
    <a href="<?php echo e(route('customers.index')); ?>" class="btn btn-secondary">Cancel</a>
</div>


<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function() {

            $('select').selectize();

            // ── Contact row template ─────────────────────────────
            // Build designation options from existing select (avoids PHP in JS)
            const designationOptions =
                `<?php $__currentLoopData = $designations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $designation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($id); ?>"><?php echo e($designation); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>`;

            const newRow = () => `
        <tr>
            <td><input type="text"  name="person_name[]"    class="form-control" placeholder="Contact name"></td>
            <td><input type="date"  name="birth_date[]"     class="form-control"></td>
            <td><input type="date"  name="marriage_date[]"  class="form-control"></td>
            <td><input type="text"  name="contact_number[]" class="form-control" placeholder="Phone number"></td>
            <td style="min-width: 12rem;">
                <select name="designation_id[]" class="form-control">
                    <option value="">Choose...</option>
                    ${designationOptions}
                </select>
            </td>
        </tr>`;

            // Add row
            $('#add-contact-row').on('click', function() {
                $('#contact-tbody').append(newRow());
            });

            // Remove last row (keep at least one)
            $('#remove-contact-row').on('click', function() {
                const rows = $('#contact-tbody tr');
                if (rows.length > 1) {
                    rows.last().remove();
                }
            });

        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH D:\Data\smartk-crm\resources\views/customers/form.blade.php ENDPATH**/ ?>