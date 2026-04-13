<form action="<?php echo e(route('customers.store')); ?>" method="POST" id="new-customer">
    <div class="modal fade" id="customer-modal">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h6 class="modal-title">New Cusomer</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

            <?php echo csrf_field(); ?>

            <div class="mb-3">
                <label for="" class="form-label">Name</label>
                <input type="text" class="form-control" name="name">
            </div>
        

            <div class="mb-3">
                <label for="" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo e(old('email')); ?>">
            </div>
            
            
            <div class="mb-3">
                <label for="" class="form-label">Phone No.</label>
                <input type="text" class="form-control" name="phone_number" value="<?php echo e(old('phone_number')); ?>">
            </div>

        </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
    </div>
</form>


<?php $__env->startPush('scripts'); ?>

<script>

  
    $(document).ready(() => {

        let customerModal = new bootstrap.Modal(document.getElementById('customer-modal'));

        $('#add-customer').click(()=>{ customerModal.show() });

        
       $('form#new-customer').on('submit', function(e){

            e.preventDefault();

            let customerEl = $('[name=customer_id]');

            $.ajax({
                url : '<?php echo e(route("customers.store")); ?>',
                method : 'POST',
                data : $(this).serialize()
            }).done((res)=>{
                
                console.log(res);
                
                customerEl[0].selectize.destroy();
                customerEl.html(res);
                customerEl.selectize();

                customerModal.hide();
                $(this).trigger('reset');

            }).fail(error => {

                console.log(error);
                const errors = error.responseJSON.errors;

                $.each(errors, (key,value)=>{
                    $(`[name='${key}']`).addClass('is-invalid');
                    $(`[name='${key}'] + div`).remove();
                    $(`[name='${key}']`).after(`<div class="invalid-feedback">${value}</div>`);
                });

            });

        });

    });

</script>

<?php $__env->stopPush(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/customers/modal.blade.php ENDPATH**/ ?>