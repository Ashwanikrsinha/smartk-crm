
<aside class="col-10 col-md-3 col-xl-2 px-0 shadow-sm bg-white position-fixed
    top-0 left-0 h-100 sidebar overflow-auto d-print-none" id="sidebar" style="z-index: 100;">
    <img src="<?php echo e(asset('assets/img/newgenguru.png')); ?>" class="d-block mx-auto my-4 pb-2" width="140" alt="newgenguru-logo">
    <section class="list-group rounded-0">

        <a href="<?php echo e(route('dashboard')); ?>" class="list-group-item list-group-item-action active border-0 mx-auto rounded" style="width: 96%;">
            <i class="feather icon-home me-2" style="margin-left: -0.2rem;"></i> Dashboard
        </a>


      

       <?php if(auth()->user()->can('viewAny', App\Models\Product::class)
        || auth()->user()->can('viewAny', App\Models\Employee::class)
        || auth()->user()->can('viewAny', App\Models\Transport::class)
        || auth()->user()->can('viewAny', App\Models\Supplier::class)
        || auth()->user()->can('viewAny', App\Models\Customer::class)): ?>
         <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between"
             data-bs-toggle="collapse" href="#general-collapse" role="button">
             <span>
                 <i class="feather icon-grid me-2"></i> General
             </span>
             <i class="feather icon-chevron-down"></i>
         </a>
         <div class="collapse" id="general-collapse" data-bs-parent="#sidebar">
             <div class="list-group">

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Product::class)): ?>
                <a href="<?php echo e(route('products.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-box me-2"></i> Products
                </a>
                <?php endif; ?>


                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Customer::class)): ?>
                <a href="<?php echo e(route('customers.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-user-check me-2"></i> Customers
                </a>
                <?php endif; ?>


                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Supplier::class)): ?>
                <a href="<?php echo e(route('suppliers.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-users me-2"></i> Suppliers
                </a>
                <?php endif; ?>


                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Employee::class)): ?>
                <a href="<?php echo e(route('employees.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-user me-2"></i> Employees
                </a>
                <?php endif; ?>


                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Transport::class)): ?>
                <a href="<?php echo e(route('transports.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-truck me-2"></i> Transports
                </a>
                <?php endif; ?>

             </div>
         </div>
     <?php endif; ?>

       

        <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between"
            data-bs-toggle="collapse" href="#account-collapse" role="button">
            <span>
                <i class="feather icon-book-open me-2"></i> Account
            </span>
            <i class="feather icon-chevron-down"></i>
        </a>
        <div class="collapse" id="account-collapse" data-bs-parent="#sidebar">
            <div class="list-group">


                <a href="#" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-users me-2"></i> Accounts
                </a>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Bill::class)): ?>
                <a href="<?php echo e(route('bills.index', ['type' => App\Models\Bill::SALE])); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-book me-2"></i> Sale Bill
                </a>
                <?php endif; ?>

                


                <a href="<?php echo e(route('bills.index', ['type' => App\Models\Bill::PURCHASE])); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-book me-2"></i> Purchase Bill
                </a>


                <a href="#" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-credit-card me-2"></i> Purchase Payment
                </a>

                <a href="#" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-corner-down-left me-2"></i> Purchase Return
                </a>

                <a href="#" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-message-circle me-2"></i> E Way Bill
                </a>

                <a href="#" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-message-square me-2"></i> E Invoice
                </a>


                <a href="#" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-book me-2"></i> Ledger
                </a>

            </div>
        </div>




       

       <?php if(auth()->user()->can('viewAny', App\Models\Purpose::class)
          || auth()->user()->can('viewAny', App\Models\Event::class)
          || auth()->user()->can('viewAny', App\Models\News::class)
          || auth()->user()->can('viewAny', App\Models\Visit::class)
          || auth()->user()->can('viewAny', App\Models\Invoice::class)
          || auth()->user()->can('viewAny', App\Models\Quotation::class)): ?>
        <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between"
            data-bs-toggle="collapse" href="#crm-collapse" role="button">
            <span>
                <i class="feather icon-calendar me-2"></i> CRM
            </span>
            <i class="feather icon-chevron-down"></i>
        </a>
        <div class="collapse" id="crm-collapse" data-bs-parent="#sidebar">
            <div class="list-group">

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\News::class)): ?>
                <a href="<?php echo e(route('news.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-message-circle me-2"></i> News
                </a>
                <?php endif; ?>


                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Visit::class)): ?>
                <a href="<?php echo e(route('visits.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-calendar me-2"></i> Visits
                </a>
                <?php endif; ?>


                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Target::class)): ?>
                <a href="<?php echo e(route('targets.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-target me-2"></i> Visit Targets
                </a>
                <?php endif; ?>


                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Quotation::class)): ?>
                <a href="<?php echo e(route('quotations.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-book me-2"></i> Quotations
                </a>
                <?php endif; ?>


                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Invoice::class)): ?>
                <a href="<?php echo e(route('invoices.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-layers me-2"></i> Performa Invoices
                </a>
                <?php endif; ?>


                <a href="<?php echo e(route('tasks.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-bell me-2"></i> Tasks
                </a>

                <a href="<?php echo e(route('leaves.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-bookmark me-2"></i> Leaves
                </a>

                <a href="#" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-user-x me-2"></i> Attendance
                </a>


            </div>
        </div>
        <?php endif; ?>



        <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between"
            data-bs-toggle="collapse" href="#tally-collapse" role="button">
            <span>
                <i class="feather icon-cpu me-2"></i> Tally
            </span>
            <i class="feather icon-chevron-down"></i>
        </a>
        <div class="collapse" id="tally-collapse" data-bs-parent="#sidebar">
            <div class="list-group">

                <a href="<?php echo e(route('tally.items.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-archive me-2"></i> Items
                </a>


                <a href="<?php echo e(route('tally.sales.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-book me-2"></i> Sales
                </a>

                <a href="<?php echo e(route('tally.purchases.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-book me-2"></i> Purchases
                </a>


                <a href="<?php echo e(route('tally.payments.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-message-square me-2"></i> Payments
                </a>

                <a href="<?php echo e(route('tally.receipts.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-message-circle me-2"></i> Receipts
                </a>


                <a href="<?php echo e(route('tally.ledgers.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-bookmark me-2"></i> Ledger
                </a>

                <a href="<?php echo e(route('tally.stocks.index')); ?>" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-archive me-2"></i> Godown Stock
                </a>

            </div>
        </div>


        <a href="<?php echo e(route('reports.index')); ?>" class="list-group-item list-group-item-action border-0">
            <i class="feather icon-file-text me-2"></i> Reports
        </a>


        <a href="<?php echo e(route('settings.index')); ?>" class="list-group-item list-group-item-action border-0">
            <i class="feather icon-settings me-2"></i> Settings
        </a>


        <a href="<?php echo e(route('notifications.index')); ?>" class="list-group-item list-group-item-action border-0">
            <i class="feather icon-bell me-2"></i> Notifications
        </a>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $company)): ?>
        <a href="<?php echo e(route('companies.edit', ['company' => $company ])); ?>" class="list-group-item list-group-item-action border-0">
            <i class="feather icon-home me-2"></i> Company
        </a>
        <?php endif; ?>


        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\User::class)): ?>
        <a href="<?php echo e(route('users.index')); ?>" class="list-group-item list-group-item-action border-0">
            <i class="feather icon-users me-2"></i> Users
        </a>
        <?php endif; ?>


</aside>

<div class="d-none position-fixed top-0 left-0 w-100 h-100" id="sidebar-overlay"
    style="background: rgb(0 0 0 / 50%);z-index: 99;"></div>
<?php /**PATH D:\Data\smartk-crm\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>