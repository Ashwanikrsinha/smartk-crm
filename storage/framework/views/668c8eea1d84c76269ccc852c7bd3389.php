<?php echo csrf_field(); ?>
<div class="row">
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Quotation Date</label>
        <input type="date" class="form-control" name="quotation_date"
            value="<?php echo e(isset($quotation) ? $quotation->quotation_date->format('Y-m-d') : date('Y-m-d')); ?>" required>
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Customer Name</label>
        <select name="customer_id" id="customer_id" class="form-control" required>
            <option selected value="">Choose...</option>
            <?php if(isset($quotation)): ?>
            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($key); ?>" <?php echo e(request('customer_id')==$key ? 'selected' : ''); ?> <?php echo e(!request('customer_id') &&
                $quotation->customer_id == $key ? 'selected' : ''); ?>>
                <?php echo e($customer); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option <?php echo e(request('customer_id') == $key || old('customer_id')==$key ? 'selected' : ''); ?> value="<?php echo e($key); ?>"><?php echo e($customer); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Visit No.</label>
        <select name="visit_id" id="" class="form-control" required>
           <option value="" selected disabled>Choose...</option>
           <?php if(isset($quotation) && blank($visits)): ?>)
              <option value="<?php echo e($quotation->visit->id); ?>" selected>V-<?php echo e($quotation->visit->visit_number); ?></option>
           <?php elseif(isset($visits)): ?>
                <?php $__currentLoopData = $visits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $visit_number): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>" <?php echo e(request('visit_id') == $key ? 'selected' : ''); ?>>
                    V-<?php echo e($visit_number); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
           <?php endif; ?>
        </select>
    </div>


    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Assign To (User)</label>
        <select name="user_id" id="user_id" class="form-control" required>
            <?php if(isset($quotation)): ?>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($key); ?>" <?php echo e($quotation->user_id == $key ? 'selected' : ''); ?>>
                <?php echo e($user); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <option selected value="">Choose...</option>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option <?php echo e(old('user_id')==$key ? 'selected' : ''); ?> value="<?php echo e($key); ?>"><?php echo e($user); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Status</label>
        <select name="status" id="status" class="form-control" required>
            <option selected value="">Choose...</option>
            <?php if(isset($quotation)): ?>
            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($status); ?>" <?php echo e($quotation->status == $status ? 'selected' : ''); ?>>
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
    
</div>


<?php if(isset($visit) && $mode == 'create'): ?>
    <?php echo $__env->make('visits.items', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 
<?php elseif(!isset($visit) && $mode == 'edit'): ?>
    <?php echo $__env->make('quotations.items', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php elseif(isset($visit) && $mode == 'edit'): ?>
    <?php echo $__env->make('quotations.items', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>


<section class="row <?php echo e(isset($quotation) && $quotation->status == App\Models\Visit::FOLLOW_UP ? '' : 'd-none'); ?>" id="follow-up">

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Follow Up Date</label>
        <input type="date" class="form-control" name="follow_date" value="<?php echo e(isset($quotation->follow_date) ? $quotation->follow_date->format('Y-m-d') : old('follow_date')); ?>">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Follow Up Type</label>
        <select name="follow_type" id="follow_type" class="form-control">
            <option selected value="" disabled>Choose...</option>
            <?php if(isset($quotation)): ?>
            <?php $__currentLoopData = $follow_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($type); ?>" <?php echo e($quotation->follow_type == $type ? 'selected' : ''); ?>>
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


<div class="<?php echo e(isset($quotation) && $quotation->status == App\Models\Quotation::NOT_INTERESTED ? '' : 'd-none'); ?> mb-3" id="reason">
    <label for="" class="form-label">Reason</label>
    <textarea name="reason" cols="30" rows="3" class="form-control"><?php echo e($quotation->reason  ?? old('reason')); ?></textarea>
</div>


<div class="mb-3">
    <label for="" class="form-label">Remarks</label>
    <textarea name="remarks" cols="30" rows="3" class="form-control"><?php echo e($quotation->remarks ?? old('remarks')); ?></textarea>
</div>


<div class="mb-3">
    <label for="attachemnts" class="form-label">Attachments</label>
    <input type="file" name="attachments[]" multiple max="3" id="attachemnts" class="shadow-sm">
    <small class="text-muted d-block" style="transform: translateY(-6px);">
        <strong>Formats</strong>: images, pdf, excel, docx.</small>
</div>

<button type="submit" class="btn btn-primary"><?php echo e($mode == 'create' ? 'Save' : 'Edit'); ?></button>


<?php $__env->startPush('scripts'); ?>

<script>
    function enableSelectize(){
       $('table#item tbody').find('select').selectize({ sortField: 'text' });
    }  
  

    $(document).ready(() => {

        $('select').selectize();

        $('input[type=date]').flatpickr({
            altInput: true,
            altFormat: 'F j, Y',
            dateFormat: 'Y-m-d',
        });


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


        
        FilePond.create(document.querySelector('#attachemnts'));

        FilePond.setOptions({
            server : {
                process : {  
                    url : `<?php echo e(route('quotations.attachments.store')); ?>`,
                    headers : { 'X-CSRF_TOKEN' : '<?php echo e(csrf_token()); ?>' },
                    onerror : (json) => { console.log(JSON.parse(json)); }
                },
                revert: {
                  url : `<?php echo e(route('quotations.attachments.destroy')); ?>`,
                  _method : 'DELETE',
                  headers : { 'X-CSRF_TOKEN' : '<?php echo e(csrf_token()); ?>' }
                }
            }
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
   
       $('#check-items').click(function(){
   
           $('table#item tbody').find('select').each(function (el) {
               let value = $(this).val();
               $(this)[0].selectize.destroy();
               $(this).val(value);
           });
   
           if($(this).is(':checked')){
               $('table#item tbody').find('[name]').prop('disabled', false);
               $('table#item tbody').find('[name]').prop('required', true);
               $('table#item tbody').find(`[name='descriptions[]']`).prop('required', false);
           }else{
               $('table#item tbody').find('[name]').prop('disabled', true);
               $('table#item tbody').find('[name]').prop('required', false);
           }
           
           enableSelectize();
       
       });

       
       $('select#status').change(function(){

            const followUp = '<?php echo e(App\Models\Quotation::FOLLOW_UP); ?>';
            const notInterested = '<?php echo e(App\Models\Quotation::NOT_INTERESTED); ?>';

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

<?php $__env->stopPush(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/quotations/form.blade.php ENDPATH**/ ?>