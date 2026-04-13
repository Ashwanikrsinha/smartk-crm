<?php echo csrf_field(); ?>
<div class="row">
    
    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Invoice No.</label>
        <div class="form-control"><?php echo e($invoice->invoice_number ?? $invoice_number); ?></div>
    </div>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Invoice Date</label>
        <input type="date" class="form-control" name="invoice_date"
            value="<?php echo e(isset($invoice) ? $invoice->invoice_date->format('Y-m-d') : date('Y-m-d')); ?>" required>
    </div>
    
    
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Customer Name</label>
        <select name="customer_id" id="customer_id" class="form-control" required>
            <?php if(isset($invoice)): ?>
            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($key); ?>" <?php echo e(request('customer_id')==$key ? 'selected' : ''); ?> <?php echo e(!request('customer_id') &&
            $invoice->customer_id == $key ? 'selected' : ''); ?>>
                <?php echo e($customer); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <option selected value="">Choose...</option>
            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option <?php echo e(request('customer_id') == $key || old('customer_id')==$key ? 'selected' : ''); ?>  value="<?php echo e($key); ?>"><?php echo e($customer); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Visit No.</label>
        <select name="visit_id" id="" class="form-control" required>
           <option value="" selected disabled>Choose...</option>
           <?php if(isset($invoice) && blank($visits)): ?>)
              <option value="<?php echo e($invoice->visit->id); ?>" selected>V-<?php echo e($invoice->visit->visit_number); ?></option>
           <?php elseif(isset($visits)): ?>
                <?php $__currentLoopData = $visits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $visit_number): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>" <?php echo e(request('visit_id') == $key ? 'selected' : ''); ?>>
                    V-<?php echo e($visit_number); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
           <?php endif; ?>
        </select>
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Status</label>
        <select name="status" id="status" class="form-control" required>
            <option selected value="">Choose...</option>
            <?php if(isset($invoice)): ?>
            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($status); ?>" <?php echo e($invoice->status == $status ? 'selected' : ''); ?>>
                <?php echo e(ucwords($status)); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option <?php echo e(old('status')==$status ? 'selected' : ''); ?> value="<?php echo e($status); ?>">
                <?php echo e(ucwords($status)); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>

    
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Phone No.</label>
        <input type="text" class="form-control" name="phone_number" value="<?php echo e($invoice->phone_number ?? old('phone_number')); ?>">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Address</label>
        <input type="text" class="form-control" name="address" value="<?php echo e($invoice->address ?? old('address')); ?>">
    </div>
    


</div>


<?php echo $__env->make('invoices.items', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<section class="row <?php echo e(isset($invoice) && $invoice->status == App\Models\Visit::FOLLOW_UP ? '' : 'd-none'); ?>" id="follow-up">

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Follow Up Date</label>
        <input type="date" class="form-control" name="follow_date" value="<?php echo e(isset($invoice->follow_date) ? $invoice->follow_date->format('Y-m-d') : old('follow_date')); ?>">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Follow Up Type</label>
        <select name="follow_type" id="follow_type" class="form-control">
            <option selected value="" disabled>Choose...</option>
            <?php if(isset($invoice)): ?>
            <?php $__currentLoopData = $follow_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($type); ?>" <?php echo e($invoice->follow_type == $type ? 'selected' : ''); ?>>
                <?php echo e(ucfirst($type)); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <?php $__currentLoopData = $follow_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option <?php echo e(old('follow_type')==$type ? 'selected' : ''); ?> value="<?php echo e($type); ?>"><?php echo e(ucfirst($type)); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>


</section>


<div class="<?php echo e(isset($invoice) && $invoice->status == App\Models\Invoice::NOT_INTERESTED ? '' : 'd-none'); ?> mb-3" id="reason">
    <label for="" class="form-label">Reason</label>
    <textarea name="reason" cols="30" rows="3" class="form-control"><?php echo e($invoice->reason  ?? old('reason')); ?></textarea>
</div>



<footer class="d-flex justify-content-between mt-3 mt-lg-0 mb-4">
    <button class="btn btn-primary btn-sm" id="add-row">
        <span class="feather icon-plus"></span>
    </button>
    <button class="btn btn-danger btn-sm" id="remove-row">
        <i class="feather icon-x"></i>
    </button>
</footer>


<div class="mb-3">
    <label for="" class="form-label">Remarks</label>
    <textarea name="remarks" cols="30" rows="3"  class="form-control"><?php echo e($invoice->remarks ?? old('remarks')); ?></textarea>
</div>


<div class="mb-3">
    <label for="" class="form-label">Terms & Conditions</label>
    <textarea name="terms" cols="30" rows="3"  class="form-control"><?php echo e($invoice->terms ?? old('terms')); ?></textarea>
</div>


<div id="filepond-alert" class="alert alert-danger d-none my-3">
    Only images, pdf, doc, excel files are allowed with max size of 10MB.
</div>

<div class="mb-3">
    <label for="attachemnts" class="form-label">Attachments</label>
    <input type="file" name="attachments[]" multiple max="3" id="attachemnts">
</div>

<button type="submit" class="btn btn-primary"><?php echo e($mode == 'create' ? 'Save' : 'Edit'); ?></button>


<?php $__env->startPush('scripts'); ?>

<script>
    
    function enableSelectize(){
       $('table#item tbody').find('select').selectize({ sortField: 'text' });
    }  
  
    $(document).ready(() => {

        
        $('select').selectize();


        $('table#item tbody').on('input',`input[name='quantities[]']`,function(){

            let rateEl = $(this).parent().parent().find(`input[name='rates[]']`);

            rateEl.val() ?  rateEl.val() : rateEl.val(0);

            let total = $(this).val() * rateEl.val();

            $(this).parent().parent().find(`input[name='amounts[]']`).val(total.toFixed(2));

            setAmount();
            setTotalAmount();

        });


        $('table#item tbody').on('input',`input[name='rates[]']`,function(){

            let quantityEl = $(this).parent().parent().find(`input[name='quantities[]']`);

            quantityEl.val() ?  quantityEl.val() : quantityEl.val(0);

            let total = $(this).val() * quantityEl.val();

            $(this).parent().parent().find(`input[name='amounts[]']`).val(total.toFixed(2));
            
            setAmount();
            setTotalAmount();

        });


        $('#add-row').click(function(e){
            
            e.preventDefault();
   
           $('table#item tbody tr:last').find('select').each(function (el) {
               let value = $(this).val();
               $(this)[0].selectize.destroy();
               $(this).val(value);
           });
   
           $('table#item tbody tr:last').clone()
               .appendTo('table#item tbody')
               .find('[name]').val('');
               
            enableSelectize();
       }); 
   
   
        // remove last row
        $('#remove-row').on('click', (e)=>{
           e.preventDefault();
           $('table#item tbody tr:last').remove();
       });


        function setAmount(){

            let amount = 0;
                
            $(`input[name='amounts[]']`).each(function(index, el){
                amount = amount + parseFloat($(this).val());        
            });
            console.log(amount);
            $('[name=amount]').val(amount.toFixed(2));
        }


        
        $('select[name=customer_id]').on('change', function(){
            if ($(this).val().length > 0) {
              window.location = `${window.location.origin}${window.location.pathname}?customer_id=${$(this).val()}`;   
            }
        }); 


        $('select[name=visit_id]').on('change', function(){
            if ($(this).val().length > 0) {
               window.location = `${window.location.origin}${window.location.pathname}?customer_id=${$('[name=customer_id]').val()}&visit_id=${$(this).val()}`;
            }
        }); 


        

        
        const filePondAlertEl = $('#filepond-alert');

        FilePond.create(document.querySelector('#attachemnts'));

        FilePond.setOptions({
            server : {
                headers : { 
                      'X-CSRF_TOKEN' : '<?php echo e(csrf_token()); ?>',
                      'X-Requested-With': 'XMLHttpRequest', 
                },
                process : {  
                    url : `<?php echo e(route('invoices.attachments.store')); ?>`,
                    onerror : (res) => {
                        console.log(res);
                        filePondAlertEl.removeClass('d-none');
                        setTimeout(() => filePondAlertEl.addClass('d-none'), 4000);
                    }
                },
                revert: {
                  url : `<?php echo e(route('invoices.attachments.destroy')); ?>`,
                  _method : 'DELETE',
                }
            }
        });
   

       tinymce.init({
            selector: '[name=terms]',
            height: 420,
            branding: false,
            plugins: 'lists link image paste table fullscreen',
            toolbar: `undo redo | bold italic underline | alignleft
                    aligncenter alignright alignjustify | bullist numlist outdent indent 
                    | table |link image | fullscreen`,
        });

        
       $('select#status').change(function(){

            const followUp = '<?php echo e(App\Models\Invoice::FOLLOW_UP); ?>';
            const notInterested = '<?php echo e(App\Models\Invoice::NOT_INTERESTED); ?>';

            $('section#follow-up').addClass('d-none');
            $('div#reason').addClass('d-none');


            if($(this).val() == followUp){
                $('section#follow-up').removeClass('d-none');
            }

            if($(this).val() == notInterested){
                $('div#reason').removeClass('d-none');
            }  

        });
   
   
        
    });

</script>

<?php $__env->stopPush(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/invoices/form.blade.php ENDPATH**/ ?>