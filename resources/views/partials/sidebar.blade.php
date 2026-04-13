{{-- sidebar --}}
<aside class="col-10 col-md-3 col-xl-2 px-0 shadow-sm bg-white position-fixed
    top-0 left-0 h-100 sidebar overflow-auto d-print-none" id="sidebar" style="z-index: 100;">
    <img src="{{ asset('assets/img/newgenguru.png') }}" class="d-block mx-auto my-4 pb-2" width="140" alt="newgenguru-logo">
    <section class="list-group rounded-0">

        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action active border-0 mx-auto rounded" style="width: 96%;">
            <i class="feather icon-home me-2" style="margin-left: -0.2rem;"></i> Dashboard
        </a>


      {{-- general --}}

       @if(auth()->user()->can('viewAny', App\Models\Product::class)
        || auth()->user()->can('viewAny', App\Models\Employee::class)
        || auth()->user()->can('viewAny', App\Models\Transport::class)
        || auth()->user()->can('viewAny', App\Models\Supplier::class)
        || auth()->user()->can('viewAny', App\Models\Customer::class))
         <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between"
             data-bs-toggle="collapse" href="#general-collapse" role="button">
             <span>
                 <i class="feather icon-grid me-2"></i> General
             </span>
             <i class="feather icon-chevron-down"></i>
         </a>
         <div class="collapse" id="general-collapse" data-bs-parent="#sidebar">
             <div class="list-group">

                @can('viewAny', App\Models\Product::class)
                <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-box me-2"></i> Products
                </a>
                @endcan


                @can('viewAny', App\Models\Customer::class)
                <a href="{{ route('customers.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-user-check me-2"></i> Customers
                </a>
                @endcan


                @can('viewAny', App\Models\Supplier::class)
                <a href="{{ route('suppliers.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-users me-2"></i> Suppliers
                </a>
                @endcan


                @can('viewAny', App\Models\Employee::class)
                <a href="{{ route('employees.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-user me-2"></i> Employees
                </a>
                @endcan


                @can('viewAny', App\Models\Transport::class)
                <a href="{{ route('transports.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-truck me-2"></i> Transports
                </a>
                @endcan

             </div>
         </div>
     @endif

       {{-- accounts --}}

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

                @can('viewAny', App\Models\Bill::class)
                <a href="{{ route('bills.index', ['type' => App\Models\Bill::SALE]) }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-book me-2"></i> Sale Bill
                </a>
                @endcan

                {{-- <a href="#" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-printer me-2"></i> Sale Receipt
                </a> --}}


                <a href="{{ route('bills.index', ['type' => App\Models\Bill::PURCHASE]) }}" class="list-group-item list-group-item-action border-0">
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




       {{-- crm --}}

       @if(auth()->user()->can('viewAny', App\Models\Purpose::class)
          || auth()->user()->can('viewAny', App\Models\Event::class)
          || auth()->user()->can('viewAny', App\Models\News::class)
          || auth()->user()->can('viewAny', App\Models\Visit::class)
          || auth()->user()->can('viewAny', App\Models\Invoice::class)
          || auth()->user()->can('viewAny', App\Models\Quotation::class))
        <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between"
            data-bs-toggle="collapse" href="#crm-collapse" role="button">
            <span>
                <i class="feather icon-calendar me-2"></i> CRM
            </span>
            <i class="feather icon-chevron-down"></i>
        </a>
        <div class="collapse" id="crm-collapse" data-bs-parent="#sidebar">
            <div class="list-group">

                @can('viewAny', App\Models\News::class)
                <a href="{{ route('news.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-message-circle me-2"></i> News
                </a>
                @endcan


                @can('viewAny', App\Models\Visit::class)
                <a href="{{ route('visits.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-calendar me-2"></i> Visits
                </a>
                @endcan


                @can('viewAny', App\Models\Target::class)
                <a href="{{ route('targets.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-target me-2"></i> Visit Targets
                </a>
                @endcan


                @can('viewAny', App\Models\Quotation::class)
                <a href="{{ route('quotations.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-book me-2"></i> Quotations
                </a>
                @endcan


                @can('viewAny', App\Models\Invoice::class)
                <a href="{{ route('invoices.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-layers me-2"></i> Performa Invoices
                </a>
                @endcan


                <a href="{{ route('tasks.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-bell me-2"></i> Tasks
                </a>

                <a href="{{ route('leaves.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-bookmark me-2"></i> Leaves
                </a>

                <a href="#" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-user-x me-2"></i> Attendance
                </a>


            </div>
        </div>
        @endif



        <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between"
            data-bs-toggle="collapse" href="#tally-collapse" role="button">
            <span>
                <i class="feather icon-cpu me-2"></i> Tally
            </span>
            <i class="feather icon-chevron-down"></i>
        </a>
        <div class="collapse" id="tally-collapse" data-bs-parent="#sidebar">
            <div class="list-group">

                <a href="{{ route('tally.items.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-archive me-2"></i> Items
                </a>


                <a href="{{ route('tally.sales.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-book me-2"></i> Sales
                </a>

                <a href="{{ route('tally.purchases.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-book me-2"></i> Purchases
                </a>


                <a href="{{ route('tally.payments.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-message-square me-2"></i> Payments
                </a>

                <a href="{{ route('tally.receipts.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-message-circle me-2"></i> Receipts
                </a>


                <a href="{{ route('tally.ledgers.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-bookmark me-2"></i> Ledger
                </a>

                <a href="{{ route('tally.stocks.index') }}" class="list-group-item list-group-item-action border-0">
                    <i class="feather icon-archive me-2"></i> Godown Stock
                </a>

            </div>
        </div>


        <a href="{{ route('reports.index') }}" class="list-group-item list-group-item-action border-0">
            <i class="feather icon-file-text me-2"></i> Reports
        </a>


        <a href="{{ route('settings.index') }}" class="list-group-item list-group-item-action border-0">
            <i class="feather icon-settings me-2"></i> Settings
        </a>


        <a href="{{ route('notifications.index') }}" class="list-group-item list-group-item-action border-0">
            <i class="feather icon-bell me-2"></i> Notifications
        </a>

        @can('update', $company)
        <a href="{{ route('companies.edit', ['company' => $company ]) }}" class="list-group-item list-group-item-action border-0">
            <i class="feather icon-home me-2"></i> Company
        </a>
        @endcan


        @can('viewAny', App\Models\User::class)
        <a href="{{ route('users.index') }}" class="list-group-item list-group-item-action border-0">
            <i class="feather icon-users me-2"></i> Users
        </a>
        @endcan


</aside>
{{-- sidebar overlay --}}
<div class="d-none position-fixed top-0 left-0 w-100 h-100" id="sidebar-overlay"
    style="background: rgb(0 0 0 / 50%);z-index: 99;"></div>
