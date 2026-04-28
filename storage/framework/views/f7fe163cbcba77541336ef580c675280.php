<?php echo csrf_field(); ?>


<div class="bg-white rounded shadow-sm p-4 mb-4">

    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
        <h6 class="fw-bold mb-0"><i class="feather icon-home me-2 text-primary"></i>School Details</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#new-school-modal">
            <i class="feather icon-plus me-1"></i> New School
        </button>
    </div>

    <div class="row">

        
        <div class="col-lg-6 mb-3">
            <label class="form-label">School <span class="text-danger">*</span></label>
            <select name="customer_id" id="school-select" class="form-control" required>
                <option value="">Search and select school...</option>
                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($school->id); ?>"
                    <?php echo e(isset($invoice) && $invoice->customer_id == $school->id ? 'selected' : ''); ?>>
                    [<?php echo e($school->school_code); ?>] <?php echo e($school->name); ?> — <?php echo e($school->city); ?>, <?php echo e($school->state); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        
        <div class="col-lg-3 mb-3">
            <label class="form-label">School Code</label>
            <input type="text" class="form-control bg-light" id="school-code" readonly
                value="<?php echo e(isset($invoice) ? $invoice->customer->school_code : ''); ?>">
        </div>

        
        <div class="col-lg-3 mb-3">
            <label class="form-label">Lead From</label>
            <select name="lead_source_id" id="lead-source" class="form-control">
                <option value="">Select lead source...</option>
                <?php $__currentLoopData = $lead_sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($id); ?>"
                    <?php echo e(isset($invoice) && $invoice->customer->lead_source_id == $id ? 'selected' : ''); ?>>
                    <?php echo e($name); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        
        <div class="col-lg-4 mb-3">
            <label class="form-label">Contact Person Name</label>
            <input type="text" name="contact_person" class="form-control" id="contact-person"
                value="<?php echo e(isset($invoice) ? $invoice->contact_person : old('contact_person')); ?>"
                placeholder="Principal / Coordinator name">
        </div>

        
        <div class="col-lg-4 mb-3">
            <label class="form-label">Mobile Number</label>
            <input type="text" name="phone_number" class="form-control" id="school-phone"
                value="<?php echo e(isset($invoice) ? $invoice->phone_number : old('phone_number')); ?>"
                placeholder="10-digit mobile number">
        </div>

        
        <div class="col-lg-4 mb-3">
            <label class="form-label">Email ID</label>
            <input type="email" name="email" class="form-control" id="school-email"
                value="<?php echo e(isset($invoice) ? $invoice->customer->email : old('email')); ?>"
                placeholder="school@example.com">
        </div>

        
        <div class="col-lg-3 mb-3">
            <label class="form-label">PO Date <span class="text-danger">*</span></label>
            <input type="date" name="invoice_date" class="form-control" required
                value="<?php echo e(isset($invoice) ? $invoice->invoice_date->format('Y-m-d') : date('Y-m-d')); ?>">
        </div>

        
        <div class="col-lg-3 mb-3">
            <label class="form-label">Delivery Due Date</label>
            <input type="date" name="delivery_due_date" class="form-control"
                value="<?php echo e(isset($invoice) ? optional($invoice->delivery_due_date)->format('Y-m-d') : ''); ?>">
        </div>

        
        <div class="col-lg-6 mb-3">
            <label class="form-label">School Address</label>
            <input type="text" name="address" class="form-control" id="school-address"
                value="<?php echo e(isset($invoice) ? $invoice->address : old('address')); ?>"
                placeholder="Full address">
        </div>

        
        <div class="col-lg-3 mb-3">
            <label class="form-label">State</label>
            <input type="text" name="state" class="form-control" id="school-state"
                value="<?php echo e(isset($invoice) ? $invoice->customer->state : old('state')); ?>"
                placeholder="State">
        </div>

        
        <div class="col-lg-3 mb-3">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" id="school-city"
                value="<?php echo e(isset($invoice) ? $invoice->customer->city : old('city')); ?>"
                placeholder="City">
        </div>

        
        <div class="col-lg-2 mb-3">
            <label class="form-label">Pin Code</label>
            <input type="text" name="pin_code" class="form-control" id="school-pin"
                value="<?php echo e(isset($invoice) ? $invoice->customer->pin_code : old('pin_code')); ?>"
                placeholder="6-digit pin">
        </div>
        <?php if(!auth()->user()->isSalesPerson()): ?>
            <div class="col-lg-4 mb-3">
            <label class="form-label">Employee <span class="text-danger">*</span></label>
            <select name="user_id" id="user-select" class="form-control" required>
                <option value="">Search and select employee...</option>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($user->id); ?>"
                    <?php echo e(isset($invoice) && $invoice->user_id == $user->id ? 'selected' : ''); ?>>
                    <?php echo e($user->username); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <?php endif; ?>

        
        <div class="col-lg-4 mb-3">
            <label class="form-label">Link Visit <small class="text-muted">(optional)</small></label>
            <select name="visit_id" id="visit-select" class="form-control">
                <option value="">Select visit (optional)...</option>
                
                <?php if(isset($invoice) && $invoice->visit_id): ?>
                <option value="<?php echo e($invoice->visit_id); ?>" selected>
                    V-<?php echo e($invoice->visit->visit_number); ?>

                </option>
                <?php endif; ?>
            </select>
        </div>

    </div>
</div>



<div class="bg-white rounded shadow-sm p-4 mb-4">

    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
        <h6 class="fw-bold mb-0"><i class="feather icon-package me-2 text-primary"></i>Order Items</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-item-row">
            <i class="feather icon-plus me-1"></i> Add Row
        </button>
    </div>

    
    <div class="table-responsive">
    <table class="table table-bordered" id="items-table">
        <thead class="table-light">
            <tr>
                <th style="width:5%">#</th>
                <th style="width:18%">Product Type</th>
                <th style="width:18%">Product / Kit</th>
                <th style="width:9%">MRP</th>
                <th style="width:8%">Qty</th>
                <th style="width:9%">Discount (%)</th>
                <th style="width:12%">Net Sale Price</th>
                <th style="width:12%">Total Amount</th>
                <th style="width:5%"></th>
            </tr>
        </thead>
        <tbody id="items-body">

            <?php if(isset($invoice) && $invoice->invoiceItems->count()): ?>

                
                <?php $__currentLoopData = $invoice->invoiceItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('invoices.partials.item-row', [
                    'idx'     => $idx,
                    'item'    => $item,
                    'categories' => $categories,
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php else: ?>
                
                <?php echo $__env->make('invoices.partials.item-row', [
                    'idx'        => 0,
                    'item'       => null,
                    'categories' => $categories,
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>

        </tbody>
    </table>
    </div>

    
    <div class="d-flex justify-content-end mt-2">
        <div class="text-end">
            <label class="form-label fw-bold">Total PO Amount</label>
            <div class="input-group" style="width: 200px;">
                <span class="input-group-text">₹</span>
                <input type="text" class="form-control fw-bold text-end"
                       id="total-po-amount-display" readonly placeholder="0.00">
                <input type="hidden" name="total_po_amount" id="total-po-amount">
            </div>
        </div>
    </div>

</div>



<div class="bg-white rounded shadow-sm p-4 mb-4">

    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
        <h6 class="fw-bold mb-0">
            <i class="feather icon-credit-card me-2 text-primary"></i>
            Payment Cheques (PDC)
            <small class="text-muted fw-normal ms-1">— Post dated cheques from school</small>
        </h6>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-pdc-row">
            <i class="feather icon-plus me-1"></i> Add Cheque
        </button>
    </div>

    <div id="pdc-body">

        <?php if(isset($invoice) && $invoice->pdcs->count()): ?>
            <?php $__currentLoopData = $invoice->pdcs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $pdc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('invoices.partials.pdc-row', ['idx' => $idx, 'pdc' => $pdc], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <?php echo $__env->make('invoices.partials.pdc-row', ['idx' => 0, 'pdc' => null], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>

    </div>

</div>



<div class="bg-white rounded shadow-sm p-4 mb-4">
    <h6 class="fw-bold border-bottom pb-2 mb-3">
        <i class="feather icon-file-text me-2 text-primary"></i>Additional Details
    </h6>
    <div class="row">
        <div class="col-lg-6 mb-3">
            <label class="form-label">Remarks</label>
            <textarea name="remarks" class="form-control" rows="3"
                placeholder="Any notes or special instructions..."><?php echo e(isset($invoice) ? $invoice->remarks : old('remarks')); ?></textarea>
        </div>
        <div class="col-lg-6 mb-3">
            <label class="form-label">Terms & Conditions</label>
            <textarea name="terms" class="form-control" rows="3"
                placeholder="Payment terms, delivery conditions..."><?php echo e(isset($invoice) ? $invoice->terms : old('terms')); ?></textarea>
        </div>
    </div>
</div>



<div class="bg-white rounded shadow-sm p-3 d-flex justify-content-between align-items-center">
    <a href="<?php echo e(route('invoices.index')); ?>" class="btn btn-secondary">Cancel</a>
    <div class="d-flex gap-2">
        <button type="submit" name="action" value="draft" class="btn btn-outline-primary">
            <i class="feather icon-save me-1"></i> Save as Draft
        </button>
        <button type="submit" name="action" value="submit" class="btn btn-primary"
            onclick="return confirm('Submit this PO to your Sales Manager for approval?')">
            <i class="feather icon-send me-1"></i> Submit for Approval
        </button>
    </div>
</div>


<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function () {

    // ─── Selectize school dropdown ───────────────────────
    const schoolSelect = $('#school-select').selectize({
        placeholder: 'Search school by name or code...',
        onChange: function (schoolId) {
            if (!schoolId) return;
            fetchSchoolDetails(schoolId);
            fetchVisits(schoolId);
        }
    });
    const userSelect = $('#user-select').selectize({
        placeholder: 'Search employee by name or code...',
        onChange: function (userId) {
           const schoolId =  $('#school-select').val();
            if (!userId) return;
            if (!schoolId) return;
            fetchVisits(schoolId,userId);
        }
    });

    // ─── Selectize visit dropdown ────────────────────────
    $('#visit-select').selectize({ placeholder: 'Select visit (optional)...' });
    $('#user-select').selectize({ placeholder: 'Select employee ...' });


    // ─── Fetch school details via AJAX ──────────────────
    function fetchSchoolDetails(schoolId) {
        $.get(`/invoices/school/${schoolId}`, function (data) {
            $('#school-code').val(data.school_code);
            $('#school-phone').val(data.phone_number);
            $('#school-address').val(data.address);
            $('#school-state').val(data.state);
            $('#school-city').val(data.city);
            $('#school-pin').val(data.pin_code);
            $('#school-email').val(data.email);

            // Set lead source
            const leadSelectize = $('#lead-source')[0].selectize;
            if (leadSelectize && data.lead_source_id) {
                leadSelectize.setValue(data.lead_source_id);
            }
        });
    }

    // ─── Fetch visits for this school ───────────────────
    function fetchVisits(schoolId,userId) {
        $.get(`/invoices/visits/${schoolId}?user_id=${userId}`, function (visits) {
            const visitSelectize = $('#visit-select')[0].selectize;
            visitSelectize.clearOptions();
            visitSelectize.addOption({ value: '', text: 'Select visit (optional)...' });
            visits.forEach(v => {
                visitSelectize.addOption({
                    value: v.id,
                    text: `V-${v.visit_number} — ${v.visit_date}`
                });
            });
            visitSelectize.refreshOptions(false);
        });
    }

    // ─── Item rows: Category → Product chain ────────────
    let itemIndex = <?php echo e(isset($invoice) ? $invoice->invoiceItems->count() : 1); ?>;

    $('#add-item-row').on('click', function () {
        $.get(`/invoices/item-row-template?idx=${itemIndex}`, function (html) {
            $('#items-body').append(html);
            itemIndex++;
        });
    });

    // Remove row
    $(document).on('click', '.remove-item-row', function () {
        if ($('#items-body tr').length === 1) return; // keep at least 1
        $(this).closest('tr').remove();
        recalculateTotal();
    });

    // On category change → load products
    $(document).on('change', '.category-select', function () {
        const row = $(this).closest('tr');
        const categoryId = $(this).val();
        const productSelect = row.find('.product-select');

        productSelect.html('<option value="">Loading...</option>');

        $.get(`/invoices/products/${categoryId}`, function (products) {
            let options = '<option value="">Select product...</option>';
            products.forEach(p => {
                options += `<option value="${p.id}" data-mrp="${p.price}" data-rate="${p.price}">${p.name}</option>`;
            });
            productSelect.html(options);
        });
    });

    // On product change → fill MRP and Net Sale Price
    $(document).on('change', '.product-select', function () {
        const row = $(this).closest('tr');
        const selected = $(this).find(':selected');
        const mrp = parseFloat(selected.data('mrp')) || 0;
        const rate = parseFloat(selected.data('rate')) || 0;

        row.find('.mrp-input').val(mrp.toFixed(2));
        row.find('.rate-input').val(rate.toFixed(2));
        calculateRowTotal(row);
    });

    // On qty / discount / rate change → recalculate row
    $(document).on('input', '.qty-input, .discount-input, .rate-input', function () {
        calculateRowTotal($(this).closest('tr'));
    });

    function calculateRowTotal(row) {
        const qty      = parseFloat(row.find('.qty-input').val()) || 0;
        const discount = parseFloat(row.find('.discount-input').val()) || 0;
        const mrp      = parseFloat(row.find('.mrp-input').val()) || 0;

        // Net Sale Price = MRP - (MRP * discount / 100)
        const netPrice = mrp - (mrp * discount / 100);
        row.find('.rate-input').val(netPrice.toFixed(2));

        const total = qty * netPrice;
        row.find('.amount-input').val(total.toFixed(2));
        row.find('.amount-display').text('₹' + total.toFixed(2));

        recalculateTotal();
    }

    function recalculateTotal() {
        let total = 0;
        $('.amount-input').each(function () {
            total += parseFloat($(this).val()) || 0;
        });
        $('#total-po-amount-display').val('₹' + total.toFixed(2));
        $('#total-po-amount').val(total.toFixed(2));
    }

    // ─── PDC rows ────────────────────────────────────────
    let pdcIndex = <?php echo e(isset($invoice) ? $invoice->pdcs->count() : 1); ?>;

    $('#add-pdc-row').on('click', function () {
        const html = pdcRowTemplate(pdcIndex);
        $('#pdc-body').append(html);
        pdcIndex++;
    });

    $(document).on('click', '.remove-pdc-row', function () {
        $(this).closest('.pdc-row').remove();
    });

    function pdcRowTemplate(idx) {
        return `
        <div class="pdc-row border rounded p-3 mb-2 d-flex align-items-center gap-3 flex-wrap">
            <span class="badge bg-success">PDC ${idx + 1}</span>
            <div>
                <label class="form-label mb-1 small">Date</label>
                <input type="date" name="pdc_dates[]" class="form-control form-control-sm" style="width:140px">
            </div>
            <div>
                <label class="form-label mb-1 small">Cheque Number</label>
                <input type="text" name="pdc_cheque_numbers[]" class="form-control form-control-sm" placeholder="Cheque no." style="width:160px">
            </div>
            <div>
                <label class="form-label mb-1 small">Bank Name</label>
                <input type="text" name="pdc_bank_names[]" class="form-control form-control-sm" placeholder="Bank name" style="width:160px">
            </div>
            <div>
                <label class="form-label mb-1 small">Amount (₹)</label>
                <input type="number" step="0.01" name="pdc_amounts[]" class="form-control form-control-sm" placeholder="0.00" style="width:120px">
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger remove-pdc-row ms-auto mt-3">
                <i class="feather icon-trash-2"></i>
            </button>
        </div>`;
    }

    // ─── Init on page load ───────────────────────────────
    recalculateTotal();

});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH D:\Data\smartk-crm\resources\views/invoices/form.blade.php ENDPATH**/ ?>