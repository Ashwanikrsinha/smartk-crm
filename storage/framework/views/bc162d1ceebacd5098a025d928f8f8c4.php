
<?php $__env->startSection('content'); ?>

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Notifications</h5>
  
  <?php if($notifications->count() > 0): ?>
   <button class="btn btn-primary" id="mark-all">Mark all as Read</button>
   <?php else: ?>
   <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-secondary btn-sm">Back</a>
  <?php endif; ?>
</header>

<?php if($notifications->count() > 0): ?>
    
    <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card border-0 shadow-sm mb-4">
        <header class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <span>
                <i class="feather icon-bell me-1 bg-primary text-white rounded p-1"></i>
                <?php if($notification->type == App\Notifications\NewTaskNotification::class): ?>
                    <span class="badge bg-secondary">Task</span>
                <?php elseif($notification->type == App\Notifications\NewTaskCommentNotification::class): ?>
                    <span class="badge bg-secondary">Comment</span>
                <?php elseif($notification->type == App\Notifications\NewLeaveNotification::class
                        || $notification->type == App\Notifications\LeaveUpdateNotification::class): ?>
                    <span class="badge bg-secondary">Leave</span>
                <?php endif; ?>
                &bull;
                <?php echo e($notification->created_at->format('d M, Y - h:i:s A')); ?>

            </span>
            <button class="btn btn-sm text-primary mark-as-read d-none d-md-inline-block"
                data-id="<?php echo e($notification->id); ?>">
                Mark as Read
            </button>
        </header>
        
        <div class="card-body">
           <?php if($notification->type == App\Notifications\NewTaskNotification::class): ?>
                
                <span class="text-primary"><?php echo e($notification->data['assignor_name']); ?></span> assigned,  
                Task No. 
                <a href="<?php echo e(route('tasks.show', ['task' => $notification->data['id'] ])); ?>">
                    <?php echo e($notification->data['task_number']); ?>

                </a>

           <?php elseif($notification->type == App\Notifications\NewTaskCommentNotification::class): ?>
    
            <span class="text-primary"><?php echo e($notification->data['comment_by']); ?></span> commented on,  
            Task No. 
            <a href="<?php echo e(route('tasks.show', ['task' => $notification->data['id'] ])); ?>">
                <?php echo e($notification->data['task_number']); ?>

            </a>

           <?php elseif($notification->type == App\Notifications\NewLeaveNotification::class): ?>
        
            <span class="text-primary"><?php echo e($notification->data['leave_by']); ?></span> applied for,  
            Leave No. 
            <a href="<?php echo e(route('leaves.show', ['leave' => $notification->data['id'] ])); ?>">
                <?php echo e($notification->data['leave_number']); ?>

            </a>

            <?php elseif($notification->type == App\Notifications\LeaveUpdateNotification::class): ?>
        
            <span class="text-primary"><?php echo e($notification->data['leave_by']); ?></span> your,  
            Leave No.
            <a href="<?php echo e(route('leaves.show', ['leave' => $notification->data['id'] ])); ?>">
                <?php echo e($notification->data['leave_number']); ?>

            </a>
            updated and status is <span class="text-primary"><?php echo e(ucwords($notification->data['status'])); ?></span>

           <?php endif; ?>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php else: ?>
    <div class="alert bg-white shadow-sm py-3">
        <i class="feather icon-bell bg-primary p-1 rounded me-1 text-white"></i>
        No new notifications
    </div>
<?php endif; ?>


<?php $__env->stopSection(); ?>


<?php $__env->startPush('scripts'); ?>
<script>
  
  function sendMarkRequest(id = null) {
        return $.ajax("<?php echo e(route('notifications.mark')); ?>", {
            method: 'POST',
            data: {
                _token : `<?php echo e(csrf_token()); ?>`,
                id
            }
        });
    }


  $(document).ready(() => {

    $('.mark-as-read').click(function() {
        let request = sendMarkRequest($(this).data('id'));
        request.done(() => {
            $(this).parent().parent().fadeOut(500);
        });
    });

    $('#mark-all').click(function() {
        let request = sendMarkRequest();
        request.done(() => {
            $('.card').fadeOut(500);
        })
    });

    });   
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/notifications/index.blade.php ENDPATH**/ ?>