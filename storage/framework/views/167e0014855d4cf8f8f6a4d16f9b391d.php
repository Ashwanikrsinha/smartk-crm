
<?php $__env->startSection('content'); ?>

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Settings</h5>
  <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-secondary btn-sm">Back</a>
</header>

<h6 class="text-muted border-bottom pb-2 mb-4">Products</h6>

<section class="row">

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Unit::class)): ?>
        <div class="col-md-2 col-lg-3">
            <a href="<?php echo e(route('units.index')); ?>">
                <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                    <i class="feather icon-archive text-white p-1 rounded bg-primary"></i>
                    <h6 class="d-inline-block ms-2 mb-0 text-body">Units</h6>
                </article>
            </a>
        </div>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Group::class)): ?>
        <div class="col-md-2 col-lg-3">
            <a href="<?php echo e(route('groups.index')); ?>">
                <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                    <i class="feather icon-archive text-white p-1 rounded bg-success"></i>
                    <h6 class="d-inline-block ms-2 mb-0 text-body">Groups</h6>
                </article>
            </a>
        </div>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Category::class)): ?>
        <div class="col-md-2 col-lg-3">
            <a href="<?php echo e(route('categories.index')); ?>">
                <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                    <i class="feather icon-archive text-white p-1 rounded bg-danger"></i>
                    <h6 class="d-inline-block ms-2 mb-0 text-body">Categories</h6>
                </article>
            </a>
        </div>
    <?php endif; ?>

    
</section>


<h6 class="text-muted border-bottom pb-2 mb-4">Contacts</h6>

<section class="row">

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Designation::class)): ?>
        <div class="col-md-2 col-lg-3">
            <a href="<?php echo e(route('designations.index')); ?>">
                <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                    <i class="feather icon-layers text-white p-1 rounded bg-primary"></i>
                    <h6 class="d-inline-block ms-2 mb-0 text-body">Designations</h6>
                </article>
            </a>
        </div>
    <?php endif; ?>


    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Segment::class)): ?>
        <div class="col-md-2 col-lg-3">
            <a href="<?php echo e(route('segments.index')); ?>">
                <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                    <i class="feather icon-archive text-white p-1 rounded bg-success"></i>
                    <h6 class="d-inline-block ms-2 mb-0 text-body">Segments</h6>
                </article>
            </a>
        </div>
    <?php endif; ?>


    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\CustomerType::class)): ?>
        <div class="col-md-2 col-lg-3">
            <a href="<?php echo e(route('customer-types.index')); ?>">
                <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                    <i class="feather icon-archive text-white p-1 rounded bg-danger"></i>
                    <h6 class="d-inline-block ms-2 mb-0 text-body">Customer Types</h6>
                </article>
            </a>
        </div>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\SupplierType::class)): ?>
        <div class="col-md-2 col-lg-3">
            <a href="<?php echo e(route('supplier-types.index')); ?>">
                <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                    <i class="feather icon-grid text-white p-1 rounded bg-warning"></i>
                    <h6 class="d-inline-block ms-2 mb-0 text-body">Supplier Types</h6>
                </article>
            </a>
        </div>
    <?php endif; ?>


</section>



<h6 class="text-muted border-bottom pb-2 mb-4">Extra</h6>

<section class="row">


    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Department::class)): ?>
    <div class="col-md-2 col-lg-3">
        <a href="<?php echo e(route('departments.index')); ?>">
            <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                <i class="feather icon-tag text-white p-1 rounded bg-primary"></i>
                <h6 class="d-inline-block ms-2 mb-0 text-body">Departments</h6>
            </article>
        </a>
    </div>
    <?php endif; ?>

    <div class="col-md-2 col-lg-3">
        <a href="#">
            <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                <i class="feather icon-map text-white p-1 rounded bg-success"></i>
                <h6 class="d-inline-block ms-2 mb-0 text-body">Locations</h6>
            </article>
        </a>
    </div>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Transport::class)): ?>
    <div class="col-md-2 col-lg-3">
        <a href="<?php echo e(route('transports.index')); ?>">
            <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                <i class="feather icon-truck text-white p-1 rounded bg-danger"></i>
                <h6 class="d-inline-block ms-2 mb-0 text-body">Transports</h6>
            </article>
        </a>
    </div>
    <?php endif; ?>


</section>



<h6 class="text-muted border-bottom pb-2 mb-4">Roles & Permissions</h6>

<section class="row">
 
    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Permission::class)): ?>
    <div class="col-md-2 col-lg-3">
        <a href="<?php echo e(route('permissions.index')); ?>">
            <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                <i class="feather icon-unlock text-white p-1 rounded bg-primary"></i>
                <h6 class="d-inline-block ms-2 mb-0 text-body">Permissions</h6>
            </article>
        </a>
    </div>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Role::class)): ?>
    <div class="col-md-2 col-lg-3">
        <a href="<?php echo e(route('roles.index')); ?>">
            <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                <i class="feather icon-user-plus text-white p-1 rounded bg-success"></i>
                <h6 class="d-inline-block ms-2 mb-0 text-body">Roles</h6>
            </article>
        </a>
    </div>
    <?php endif; ?>

</section>




<h6 class="text-muted border-bottom pb-2 mb-4">CRM</h6>

<section class="row">
 
    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Event::class)): ?>
    <div class="col-md-2 col-lg-3">
        <a href="<?php echo e(route('events.index')); ?>">
            <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                <i class="feather icon-bookmark text-white p-1 rounded bg-primary"></i>
                <h6 class="d-inline-block ms-2 mb-0 text-body">Events</h6>
            </article>
        </a>
    </div>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Purpose::class)): ?>
    <div class="col-md-2 col-lg-3">
        <a href="<?php echo e(route('purposes.index')); ?>">
            <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                <i class="feather icon-tag text-white p-1 rounded bg-success"></i>
                <h6 class="d-inline-block ms-2 mb-0 text-body">Purposes</h6>
            </article>
        </a>
    </div>
    <?php endif; ?>

</section>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Data\smartk-crm\resources\views/settings/index.blade.php ENDPATH**/ ?>