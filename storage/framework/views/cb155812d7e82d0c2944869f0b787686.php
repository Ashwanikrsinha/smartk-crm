<?php echo csrf_field(); ?>
<div class="row">
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Visit Date</label>
        <input type="date" class="form-control" name="visit_date"
            value="<?php echo e(isset($visit) ? $visit->visit_date->format('Y-m-d') : date('Y-m-d')); ?>" required>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label d-flex align-items-center justify-content-between">
            Customer
            <span class="bg-primary text-white px-1 rounded" role="button" id="add-customer">&plus;</i>
        </label>
        <select name="customer_id" id="customer_id" class="form-control">
            <option selected value="">Choose...</option>
            <?php if(isset($visit)): ?>
            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($key); ?>" <?php echo e($visit->customer_id == $key ? 'selected' : ''); ?>>
                <?php echo e($customer); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option <?php echo e(old('customer_id') == $key ? 'selected' : ''); ?> value="<?php echo e($key); ?>"><?php echo e($customer); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Assign To (User)</label>
        <select name="user_id" id="user_id" class="form-control" required>
            <?php if(isset($visit)): ?>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($key); ?>" <?php echo e($visit->user_id == $key ? 'selected' : ''); ?>>
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

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Purpose</label>
        <select name="purpose_id" id="purpose_id" class="form-control" required>
            <?php if(isset($visit)): ?>
            <?php $__currentLoopData = $purposes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $purpose): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($key); ?>" <?php echo e($visit->purpose_id == $key ? 'selected' : ''); ?>>
                <?php echo e($purpose); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <option selected value="">Choose...</option>
            <?php $__currentLoopData = $purposes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $purpose): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option <?php echo e(old('purpose_id')==$key ? 'selected' : ''); ?> value="<?php echo e($key); ?>"><?php echo e($purpose); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>

    

    <?php echo $__env->make('visits.items', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Order Estimate</label>
        <input type="text" class="form-control" name="order_amount" value="<?php echo e($visit->order_amount ?? old('order_amount')); ?>">
    </div>

    <div class="col-lg-3 mb-3">
        <label for="" class="form-label">Level</label>
        <select name="level" id="level" class="form-control">
            <option selected value="">Choose...</option>
            <?php if(isset($visit)): ?>
            <?php $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($level); ?>" <?php echo e($visit->level == $level ? 'selected' : ''); ?>>
                <?php echo e(ucfirst($level)); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <?php $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option <?php echo e(old('level')==$level ? 'selected' : ''); ?> value="<?php echo e($level); ?>">
                <?php echo e(ucfirst($level)); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Status</label>
        <select name="status" id="status" class="form-control" required>
            <option selected value="">Choose...</option>
            <?php if(isset($visit)): ?>
            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($status); ?>" <?php echo e($visit->status == $status ? 'selected' : ''); ?>>
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


<section class="row <?php echo e(isset($visit) && $visit->status == App\Models\Visit::FOLLOW_UP ? '' : 'd-none'); ?>" id="follow-up">

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Follow Up Date</label>
        <input type="date" class="form-control" name="follow_date" value="<?php echo e(isset($visit->follow_date) ? $visit->follow_date->format('Y-m-d') : old('follow_date')); ?>">
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Follow Up Type</label>
        <select name="follow_type" id="follow_type" class="form-control">
            <option selected value="" disabled>Choose...</option>
            <?php if(isset($visit)): ?>
            <?php $__currentLoopData = $follow_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($type); ?>" <?php echo e($visit->follow_type == $type ? 'selected' : ''); ?>>
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

<div class="<?php echo e(isset($visit) && $visit->status == App\Models\Visit::NOT_INTERESTED ? '' : 'd-none'); ?> mb-3" id="reason">
    <label for="" class="form-label">Reason</label>
    <textarea name="reason" cols="30" rows="3" class="form-control"><?php echo e($visit->reason  ?? old('reason')); ?></textarea>
</div>

<div class="mb-3">
    <label for="" class="form-label">Insight</label>
    <textarea name="insight" cols="30" rows="3"
        class="form-control"><?php echo e($visit->insight ?? old('insight')); ?></textarea>
</div>

<div class="mb-4">
    <label for="" class="form-label">Action To Be Taken</label>
    <textarea name="action" cols="30" rows="3" class="form-control"><?php echo e($visit->action ?? old('action')); ?></textarea>
</div>


<div class="mb-3">
    <label for="" class="form-label">Description</label>
    <textarea name="description" cols="30" rows="5" class="form-control"><?php echo e($visit->description ?? old('description')); ?></textarea>
</div>


<div id="filepond-alert" class="alert alert-danger d-none my-3">
    Only images, pdf, doc, excel files are allowed with max size 10MB.
</div>

<div class="mb-3">
    <label for="attachemnts" class="form-label">Attachments</label>
    <input type="file" name="attachments[]" multiple max="3" id="attachemnts">
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



        tinymce.init({
            selector: '[name=description]',
            height: 420,
            branding: false,
            plugins: 'lists link image paste table fullscreen',
            toolbar: `undo redo | bold italic underline | alignleft
                    aligncenter alignright alignjustify | bullist numlist outdent indent 
                    | table |link image | fullscreen`,
        });

        

        const filePondAlertEl = $('#filepond-alert');
        FilePond.create(document.querySelector('#attachemnts'));

        FilePond.setOptions({
            server : {
                headers : { 
                      'X-CSRF-TOKEN' : '<?php echo e(csrf_token()); ?>',
                      'X-Requested-With': 'XMLHttpRequest', 
                },
                process : {  
                    url : `<?php echo e(route('visits.attachments.store')); ?>`,
                    onerror : (res) => {
                        console.log(res);
                        filePondAlertEl.removeClass('d-none');
                        setTimeout(() => filePondAlertEl.addClass('d-none'), 4000);
                    }
                },
                revert: {
                  url : `<?php echo e(route('visits.attachments.destroy')); ?>`,
                  _method : 'DELETE',
                }
            }
        })

        
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

            const followUp = '<?php echo e(App\Models\Visit::FOLLOW_UP); ?>';
            const notInterested = '<?php echo e(App\Models\Visit::NOT_INTERESTED); ?>';

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

<?php $__env->stopPush(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/visits/form.blade.php ENDPATH**/ ?>