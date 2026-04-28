
<aside
    class="col-10 col-md-3 col-xl-2 px-0 shadow-sm bg-white position-fixed
    top-0 left-0 h-100 sidebar overflow-auto d-print-none"
    id="sidebar" style="z-index: 100;">

    <img src="<?php echo e(asset('assets/img/newgenguru.png')); ?>" class="d-block mx-auto my-4 pb-2" width="140" alt="SmartK">

    <section class="list-group rounded-0">

        
        <a href="<?php echo e(route('dashboard')); ?>" class="list-group-item list-group-item-action border-0 active mx-auto rounded"
            style="width:96%">
            <i class="feather icon-home me-2"></i> Dashboard
        </a>

        
        <?php if(auth()->user()->hasPermission('browse_customers') ||
                auth()->user()->hasPermission('browse_visits') ||
                auth()->user()->hasPermission('browse_invoices')): ?>
            <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between"
                data-bs-toggle="collapse" href="#crm-collapse" role="button">
                <span><i class="feather icon-briefcase me-2"></i> Sales</span>
                <i class="feather icon-chevron-down"></i>
            </a>
            <div class="collapse <?php echo e(request()->routeIs('customers.*', 'visits.*', 'invoices.*') ? 'show' : ''); ?>"
                id="crm-collapse" data-bs-parent="#sidebar">
                <div class="list-group">

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Customer::class)): ?>
                        <a href="<?php echo e(route('customers.index')); ?>"
                            class="list-group-item list-group-item-action border-0 ps-4
                   <?php echo e(request()->routeIs('customers.*') ? 'active' : ''); ?>">
                            <i class="feather icon-home me-2"></i> Schools
                        </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Visit::class)): ?>
                        <a href="<?php echo e(route('visits.index')); ?>"
                            class="list-group-item list-group-item-action border-0 ps-4
                   <?php echo e(request()->routeIs('visits.*') ? 'active' : ''); ?>">
                            <i class="feather icon-map-pin me-2"></i> Visit Logs
                        </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Invoice::class)): ?>
                        <a href="<?php echo e(route('invoices.index')); ?>"
                            class="list-group-item list-group-item-action border-0 ps-4
                   <?php echo e(request()->routeIs('invoices.*') ? 'active' : ''); ?>">
                            <i class="feather icon-file-text me-2"></i> Purchase Orders
                        </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Target::class)): ?>
                        <a href="<?php echo e(route('targets.index')); ?>"
                            class="list-group-item list-group-item-action border-0 ps-4
                   <?php echo e(request()->routeIs('targets.*') ? 'active' : ''); ?>">
                            <i class="feather icon-target me-2"></i> Targets
                        </a>
                    <?php endif; ?>

                </div>
            </div>
        <?php endif; ?>


        
        <?php if(auth()->user()->hasPermission('browse_collections') ||
                auth()->user()->hasPermission('verify_payments') ||
                auth()->user()->hasPermission('browse_bills')): ?>

            <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between"
                data-bs-toggle="collapse" href="#accounts-collapse" role="button">
                <span><i class="feather icon-book-open me-2"></i> Accounts</span>
                <i class="feather icon-chevron-down"></i>
            </a>
            <div class="collapse <?php echo e(request()->routeIs('collections.*', 'bills.*') ? 'show' : ''); ?>"
                id="accounts-collapse" data-bs-parent="#sidebar">
                <div class="list-group">

                    <?php if(auth()->user()->hasPermission('verify_payments') || auth()->user()->hasPermission('browse_collections')): ?>
                        <a href="<?php echo e(route('collections.index')); ?>"
                            class="list-group-item list-group-item-action border-0 ps-4
                   <?php echo e(request()->routeIs('collections.*') ? 'active' : ''); ?>">
                            <i class="feather icon-dollar-sign me-2"></i> Collections
                        </a>
                        <a href="<?php echo e(route('pdcs.index')); ?>"
                            class="list-group-item list-group-item-action border-0 ps-4
                            <?php echo e(request()->routeIs('pdcs.index.*') ? 'active' : ''); ?>">
                            <i class="feather icon-dollar-sign me-2"></i> PDC
                        </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Bill::class)): ?>
                        <a href="<?php echo e(route('bills.index', ['type' => App\Models\Bill::SALE])); ?>"
                            class="list-group-item list-group-item-action border-0 ps-4
                   <?php echo e(request()->routeIs('bills.*') ? 'active' : ''); ?>">
                            <i class="feather icon-printer me-2"></i> Sales Bill
                        </a>
                    <?php endif; ?>

                </div>
            </div>
        <?php endif; ?>


        
        <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between"
            data-bs-toggle="collapse" href="#hr-collapse" role="button">
            <span><i class="feather icon-users me-2"></i> HR</span>
            <i class="feather icon-chevron-down"></i>
        </a>
        <div class="collapse <?php echo e(request()->routeIs('tasks.*', 'leaves.*') ? 'show' : ''); ?>" id="hr-collapse"
            data-bs-parent="#sidebar">
            <div class="list-group">

                <a href="<?php echo e(route('tasks.index')); ?>"
                    class="list-group-item list-group-item-action border-0 ps-4
                   <?php echo e(request()->routeIs('tasks.*') ? 'active' : ''); ?>">
                    <i class="feather icon-check-square me-2"></i> Tasks
                </a>

                <a href="<?php echo e(route('leaves.index')); ?>"
                    class="list-group-item list-group-item-action border-0 ps-4
                   <?php echo e(request()->routeIs('leaves.*') ? 'active' : ''); ?>">
                    <i class="feather icon-calendar me-2"></i> Leaves
                </a>


            </div>
        </div>


        
        <?php if(auth()->user()->hasPermission('export_reports') || auth()->user()->isAdmin() || auth()->user()->isSalesManager()): ?>
            <a href="<?php echo e(route('reports.index')); ?>"
                class="list-group-item list-group-item-action border-0
           <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>">
                <i class="feather icon-bar-chart-2 me-2"></i> Reports
            </a>
        <?php endif; ?>


        
        <?php if(auth()->user()->isAdmin()): ?>
            <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between"
                data-bs-toggle="collapse" href="#master-collapse" role="button">
                <span><i class="feather icon-grid me-2"></i> Master Data</span>
                <i class="feather icon-chevron-down"></i>
            </a>
            <div class="collapse" id="master-collapse" data-bs-parent="#sidebar">
                <div class="list-group">

                    <a href="<?php echo e(route('products.index')); ?>"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-box me-2"></i> Products
                    </a>

                    <a href="<?php echo e(route('categories.index')); ?>"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-tag me-2"></i> Product Type
                    </a>

                    <a href="<?php echo e(route('units.index')); ?>" class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-layers me-2"></i> Units
                    </a>

                    <a href="<?php echo e(route('lead-sources.index')); ?>"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-activity me-2"></i> Lead Sources
                    </a>

                    <a href="<?php echo e(route('segments.index')); ?>"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-pie-chart me-2"></i> Segments
                    </a>

                    <a href="<?php echo e(route('purposes.index')); ?>"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-bookmark me-2"></i> Purposes
                    </a>

                    <a href="<?php echo e(route('departments.index')); ?>"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-folder me-2"></i> Departments
                    </a>

                    <a href="<?php echo e(route('designations.index')); ?>"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-award me-2"></i> Designations
                    </a>

                </div>
            </div>

            
            <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between"
                data-bs-toggle="collapse" href="#admin-collapse" role="button">
                <span><i class="feather icon-shield me-2"></i> Administration</span>
                <i class="feather icon-chevron-down"></i>
            </a>
            <div class="collapse" id="admin-collapse" data-bs-parent="#sidebar">
                <div class="list-group">

                    <a href="<?php echo e(route('users.index')); ?>"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-users me-2"></i> Users
                    </a>

                    <a href="<?php echo e(route('roles.index')); ?>"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-key me-2"></i> Roles
                    </a>

                    <a href="<?php echo e(route('permissions.index')); ?>"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-lock me-2"></i> Permissions
                    </a>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $company)): ?>
                        <a href="<?php echo e(route('companies.edit', ['company' => $company])); ?>"
                            class="list-group-item list-group-item-action border-0 ps-4">
                            <i class="feather icon-settings me-2"></i> Company Settings
                        </a>
                    <?php endif; ?>

                </div>
            </div>
        <?php endif; ?>

        
        <?php if(auth()->user()->isSalesManager() && auth()->user()->hasPermission('create_sp_accounts')): ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\User::class)): ?>
                <a href="<?php echo e(route('users.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-users me-2"></i> My Team
                </a>
            <?php endif; ?>
        <?php endif; ?>

        
        <a href="<?php echo e(route('notifications.index')); ?>" class="list-group-item list-group-item-action border-0">
            <i class="feather icon-bell me-2"></i> Notifications
        </a>

    </section>

</aside>


<div class="d-none position-fixed top-0 left-0 w-100 h-100" id="sidebar-overlay"
    style="background: rgb(0 0 0 / 50%); z-index: 99;"></div>
<?php /**PATH D:\Data\smartk-crm\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>