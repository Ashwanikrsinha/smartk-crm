<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
       <span>
          <i class="feather icon-bell me-1 bg-primary text-white rounded p-1"></i> Tasks
       </span>
       <span class="badge bg-danger rounded-pill"><?php echo e($tasks->count()); ?></span>
    </div>
    <div class="card-body px-0 pt-0 table-responsive" style="max-height: 20rem; overflow-y: auto;">
      <?php if($tasks->count() > 0): ?>
          <table class="table" style="min-width: 30rem;">
             <thead>
                <tr class="text-uppercase text-muted small">
                   <th></th>
                   <th class="fw-normal">Task</th>
                   <th class="fw-normal">Status</th>
                   <th class="fw-normal">Deadline</th>
                   <th class="fw-normal">Assignee</th>
                   <th></th>
                </tr>
             </thead>
             <tbody>
                <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                      <td>
                         <a href="<?php echo e(route('tasks.show', ['task' => $task])); ?>" target="_blank">
                           <i class="feather icon-external-link me-1 text-muted"></i>
                         </a>
                      </td>
                       <td><?php echo e($task->task_number); ?></td>
                       <td>
                          <?php if(isset($task->completed_at)): ?>
                          <span class="badge alert-primary">complete</span>
                          <?php else: ?>
                              <span class="badge alert-danger">pending</span>
                          <?php endif; ?>
                       </td>
                       <td><?php echo e($task->deadline_time->format('d M - h:i A')); ?></td>
                       <td><?php echo e(Str::limit($task->assignee->username, 20)); ?></td>
                       <td>
                           <button type="button" class="btn btn-sm" data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           title="<?php echo e($task->title); ?>">
                              <i class="feather icon-info text-primary"></i>
                         </button>
                       </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
             </tbody>
          </table>
      <?php endif; ?>
    </div>
 </div>

 
<?php $__env->startPush('scripts'); ?>
    
<script>

   $(document).ready(()=>{

        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
        });

   });

</script>

<?php $__env->stopPush(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/widgets/task.blade.php ENDPATH**/ ?>