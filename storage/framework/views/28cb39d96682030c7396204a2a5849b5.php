<div class="accordion mb-4" id="visit-accordion">
    <div class="accordion-item border-0 shadow-sm">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-1">
          <span>
             <i class="feather icon-file-text text-primary me-1"></i> By Visit Date
          </span>
        </button>
      </h2>
      <div id="collapse-1" class="accordion-collapse collapse" data-bs-parent="#visit-accordion">
        <div class="accordion-body">
           <form action="<?php echo e(route('visits.export')); ?>" method="GET">
            <div class="row">
                <div class="col-lg-6">
                  <div class="input-group mb-2">
                    <span class="input-group-text bg-white">From</span>
                    <input type="date" name="from" class="form-control" required>
                  </div>
                </div>
                <div class="col-lg-5">
                  <div class="input-group mb-2">
                    <span class="input-group-text bg-white">To</span>
                    <input type="date" name="to" class="form-control" required>
                  </div>
                </div>
                <div class="col-lg-1">
                   <button type="submit" class="btn btn-primary">
                       <i class="feather icon-file-text"></i>
                   </button>
                </div>
              </div>
           </form>
        </div>
      </div>
    </div>
  
  </div><?php /**PATH D:\Data\smartk-crm\resources\views/visits/accordion.blade.php ENDPATH**/ ?>