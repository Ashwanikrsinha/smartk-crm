
<?php $__env->startSection('content'); ?>

<section class="d-flex align-items-center min-vh-100 bg-light">
  <div class="container-fluid px-4">
    <div class="row no-gutter justify-content-center">
      <div class="col-md-10 col-lg-8 shadow rounded overflow-hidden">
        <div class="row">
          <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-white border-end">
            <article class="text-center text-white">
              <img src="<?php echo e(asset('assets/img/newgenguru.png')); ?>" class="img-fluid" alt="Sangeeta Steel" width="240">
            </article>
          </div>
          <div class="col-lg-6 bg-white">
            <section class="px-3 py-4">
              <img class="d-block mx-auto my-5" src="<?php echo e(asset('assets/img/newgenguru.png')); ?>" width="160" alt="newgenguru">
              <h5 class="mb-3 text-primary font-weight-bold">Log In</h5>

              <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
              <article class="alert alert-danger alert-dismissible fade show shadow-sm">
                <small><?php echo e($message); ?></small>
                <button class="btn-close small" data-bs-dismiss="alert"></button>
              </article>
              <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

              
              <form action="<?php echo e(route('login')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="mb-3">
                  <input type="email" class="form-control py-3" name="email" placeholder="Email Address" value="<?php echo e(old('email')); ?>" required>
                  <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="text-danger mt-1"><?php echo e($message); ?></div>
                  <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-3">
                  <input type="password" class="form-control py-3" name="password" placeholder="Password" required>
                  <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="text-danger mt-1"><?php echo e($message); ?></div>
                  <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <section class="d-flex justify-content-between mb-3">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label text-muted" for="remember">Remember me</label>
                  </div>
                  <a href="#">Forget Password?</a>
                </section>
                <button type="submit" class="btn w-100 btn-primary py-2 mb-3">Login</button>
              </form>
            </section>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/login.blade.php ENDPATH**/ ?>