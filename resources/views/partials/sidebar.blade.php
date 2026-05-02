{{-- SIDEBAR — Role-aware navigation --}}
<aside
    class="col-10 col-md-3 col-xl-2 px-0 shadow-sm bg-white position-fixed
    top-0 left-0 h-100 sidebar overflow-auto d-print-none"
    id="sidebar" style="z-index:100;">

    <img src="{{ asset('assets/img/newgenguru.png') }}" class="d-block mx-auto my-4 pb-2" width="140" alt="SmartK">

    <section class="list-group rounded-0">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="list-group-item list-group-item-action border-0
           {{ request()->routeIs('dashboard') ? 'active' : '' }}
           mx-auto rounded"
            style="width:96%">
            <i class="feather icon-home me-2"></i> Dashboard
        </a>

        {{-- ══════════════════════════════════════════════════
             SALES — SP, SM, Admin
        ══════════════════════════════════════════════════ --}}
        @if (auth()->user()->hasPermission('browse_customers') ||
                auth()->user()->hasPermission('browse_visits') ||
                auth()->user()->hasPermission('browse_invoices'))
            <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between
           {{ request()->routeIs('customers.*', 'visits.*', 'invoices.*', 'targets.*') ? 'bg-light fw-bold' : '' }}"
                data-bs-toggle="collapse" href="#sales-collapse" role="button">
                <span><i class="feather icon-briefcase me-2"></i> Sales</span>
                <i class="feather icon-chevron-down"></i>
            </a>
            <div class="collapse {{ request()->routeIs('customers.*', 'visits.*', 'invoices.*', 'targets.*') ? 'show' : '' }}"
                id="sales-collapse" data-bs-parent="#sidebar">
                <div class="list-group">

                    @can('viewAny', App\Models\Customer::class)
                        <a href="{{ route('customers.index') }}"
                            class="list-group-item list-group-item-action border-0 ps-4
                   {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                            <i class="feather icon-home me-2"></i> Schools
                        </a>
                    @endcan

                    @can('viewAny', App\Models\Visit::class)
                        <a href="{{ route('visits.index') }}"
                            class="list-group-item list-group-item-action border-0 ps-4
                   {{ request()->routeIs('visits.*') ? 'active' : '' }}">
                            <i class="feather icon-map-pin me-2"></i> Visit Logs
                        </a>
                    @endcan

                    @can('viewAny', App\Models\Invoice::class)
                        <a href="{{ route('invoices.index') }}"
                            class="list-group-item list-group-item-action border-0 ps-4
                   {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                            <i class="feather icon-file-text me-2"></i> Purchase Orders
                        </a>
                    @endcan

                    @can('viewAny', App\Models\Target::class)
                        <a href="{{ route('targets.index') }}"
                            class="list-group-item list-group-item-action border-0 ps-4
                   {{ request()->routeIs('targets.*') ? 'active' : '' }}">
                            <i class="feather icon-target me-2"></i> Targets
                        </a>
                    @endcan

                </div>
            </div>
        @endif


        {{-- ══════════════════════════════════════════════════
             ACCOUNTS — Accounts Team, Admin
             (SM sees Bills read-only, no Collections)
        ══════════════════════════════════════════════════ --}}
        @if (auth()->user()->hasPermission('browse_collections') ||
                auth()->user()->hasPermission('verify_payments') ||
                auth()->user()->hasPermission('browse_bills'))

            <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between
           {{ request()->routeIs('collections.*', 'bills.*', 'pdcs.*') ? 'bg-light fw-bold' : '' }}"
                data-bs-toggle="collapse" href="#accounts-collapse" role="button">
                <span><i class="feather icon-book-open me-2"></i> Accounts</span>
                <i class="feather icon-chevron-down"></i>
            </a>
            <div class="collapse {{ request()->routeIs('collections.*', 'bills.*', 'pdcs.*') ? 'show' : '' }}"
                id="accounts-collapse" data-bs-parent="#sidebar">
                <div class="list-group">

                    {{-- Collections — Accounts + Admin only --}}
                    @if (auth()->user()->hasPermission('verify_payments') || auth()->user()->hasPermission('browse_collections'))
                        <a href="{{ route('collections.index') }}"
                            class="list-group-item list-group-item-action border-0 ps-4
                   {{ request()->routeIs('collections.*') ? 'active' : '' }}">
                            <i class="feather icon-dollar-sign me-2"></i> Collections
                        </a>
                    @endif

                    {{-- PDCs — Accounts + Admin --}}
                    @if (auth()->user()->hasPermission('browse_pdcs') || auth()->user()->isAdmin() || auth()->user()->isAccounts())
                        <a href="{{ route('pdcs.index') }}"
                            class="list-group-item list-group-item-action border-0 ps-4
                   {{ request()->routeIs('pdcs.*') ? 'active' : '' }}">
                            <i class="feather icon-credit-card me-2"></i> PDC Cheques
                        </a>
                    @endif

                    {{-- Sales Bill — Accounts + Admin (SM read-only via show page) --}}
                    @can('viewAny', App\Models\Bill::class)
                        <a href="{{ route('bills.index') }}"
                            class="list-group-item list-group-item-action border-0 ps-4
                   {{ request()->routeIs('bills.*') ? 'active' : '' }}">
                            <i class="feather icon-printer me-2"></i> Sales Bills
                        </a>
                    @endcan

                </div>
            </div>
        @endif


        {{-- ══════════════════════════════════════════════════
             WAREHOUSE — Warehouse role + Admin
        ══════════════════════════════════════════════════ --}}
        @if (auth()->user()->hasPermission('browse_dispatch_queue') || auth()->user()->isAdmin())
            <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between
           {{ request()->routeIs('dispatches.*') ? 'bg-light fw-bold' : '' }}"
                data-bs-toggle="collapse" href="#warehouse-collapse" role="button">
                <span><i class="feather icon-truck me-2"></i> Warehouse</span>
                <i class="feather icon-chevron-down"></i>
            </a>
            <div class="collapse {{ request()->routeIs('dispatches.*') ? 'show' : '' }}" id="warehouse-collapse"
                data-bs-parent="#sidebar">
                <div class="list-group">

                    <a href="{{ route('dispatches.index') }}"
                        class="list-group-item list-group-item-action border-0 ps-4
                   {{ request()->routeIs('dispatches.*') ? 'active' : '' }}">
                        <i class="feather icon-package me-2"></i> Dispatch List
                    </a>

                </div>
            </div>
        @endif


        {{-- ══════════════════════════════════════════════════
             HR — All roles (own data scoped in controller)
        ══════════════════════════════════════════════════ --}}
        <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between
           {{ request()->routeIs('tasks.*', 'leaves.*') ? 'bg-light fw-bold' : '' }}"
            data-bs-toggle="collapse" href="#hr-collapse" role="button">
            <span><i class="feather icon-users me-2"></i> HR</span>
            <i class="feather icon-chevron-down"></i>
        </a>
        <div class="collapse {{ request()->routeIs('tasks.*', 'leaves.*') ? 'show' : '' }}" id="hr-collapse"
            data-bs-parent="#sidebar">
            <div class="list-group">

                <a href="{{ route('tasks.index') }}"
                    class="list-group-item list-group-item-action border-0 ps-4
                   {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                    <i class="feather icon-check-square me-2"></i> Tasks
                </a>

                <a href="{{ route('leaves.index') }}"
                    class="list-group-item list-group-item-action border-0 ps-4
                   {{ request()->routeIs('leaves.*') ? 'active' : '' }}">
                    <i class="feather icon-calendar me-2"></i> Leaves
                </a>

            </div>
        </div>


        {{-- ══════════════════════════════════════════════════
             REPORTS — SM, Accounts, Admin
        ══════════════════════════════════════════════════ --}}
        @if (auth()->user()->hasPermission('export_reports') ||
                auth()->user()->isAdmin() ||
                auth()->user()->isSalesManager() ||
                auth()->user()->isAccounts())
            <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between
           {{ request()->routeIs('reports.*') ? 'bg-light fw-bold' : '' }}"
                data-bs-toggle="collapse" href="#reports-collapse" role="button">
                <span><i class="feather icon-bar-chart-2 me-2"></i> Reports</span>
                <i class="feather icon-chevron-down"></i>
            </a>
            <div class="collapse {{ request()->routeIs('reports.*') ? 'show' : '' }}" id="reports-collapse"
                data-bs-parent="#sidebar">
                <div class="list-group">

                    <a href="{{ route('reports.index') }}"
                        class="list-group-item list-group-item-action border-0 ps-4
                   {{ request()->is('reports') ? 'active' : '' }}">
                        <i class="feather icon-file-text me-2"></i> PO Reports
                    </a>

                    <a href="{{ route('reports.po-log-all') }}"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-activity me-2"></i> PO Activity Log
                    </a>

                </div>
            </div>
        @endif


        {{-- ══════════════════════════════════════════════════
             MASTER DATA — Admin only
        ══════════════════════════════════════════════════ --}}
        @if (auth()->user()->isAdmin())
            <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between"
                data-bs-toggle="collapse" href="#master-collapse" role="button">
                <span><i class="feather icon-grid me-2"></i> Master Data</span>
                <i class="feather icon-chevron-down"></i>
            </a>
            <div class="collapse" id="master-collapse" data-bs-parent="#sidebar">
                <div class="list-group">

                    <a href="{{ route('products.index') }}"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-box me-2"></i> Products
                    </a>
                    <a href="{{ route('categories.index') }}"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-tag me-2"></i> Categories
                    </a>
                    <a href="{{ route('units.index') }}"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-layers me-2"></i> Units
                    </a>
                    <a href="{{ route('lead-sources.index') }}"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-activity me-2"></i> Lead Sources
                    </a>
                    <a href="{{ route('segments.index') }}"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-pie-chart me-2"></i> Segments
                    </a>
                    <a href="{{ route('purposes.index') }}"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-bookmark me-2"></i> Purposes
                    </a>
                    <a href="{{ route('departments.index') }}"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-folder me-2"></i> Departments
                    </a>
                    <a href="{{ route('designations.index') }}"
                        class="list-group-item list-group-item-action border-0 ps-4">
                        <i class="feather icon-award me-2"></i> Designations
                    </a>

                </div>
            </div>


            {{-- Administration --}}
            <a class="list-group-item list-group-item-action border-0 d-flex justify-content-between"
                data-bs-toggle="collapse" href="#admin-collapse" role="button">
                <span><i class="feather icon-shield me-2"></i> Administration</span>
                <i class="feather icon-chevron-down"></i>
            </a>
            <div class="collapse" id="admin-collapse" data-bs-parent="#sidebar">
                <div class="list-group">

                    <a href="{{ route('users.index') }}"
                        class="list-group-item list-group-item-action border-0 ps-4
                   {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="feather icon-users me-2"></i> Users
                    </a>
                    <a href="{{ route('roles.index') }}"
                        class="list-group-item list-group-item-action border-0 ps-4
                   {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                        <i class="feather icon-key me-2"></i> Roles
                    </a>
                    <a href="{{ route('permissions.index') }}"
                        class="list-group-item list-group-item-action border-0 ps-4
                   {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                        <i class="feather icon-lock me-2"></i> Permissions
                    </a>
                    @can('update', $company)
                        <a href="{{ route('companies.edit', ['company' => $company]) }}"
                            class="list-group-item list-group-item-action border-0 ps-4">
                            <i class="feather icon-settings me-2"></i> Company Settings
                        </a>
                    @endcan

                </div>
            </div>
        @endif


        {{-- SM can see team (create SP accounts) --}}
        @if (auth()->user()->isSalesManager() && auth()->user()->hasPermission('create_sp_accounts'))
            <a href="{{ route('users.index') }}"
                class="list-group-item list-group-item-action border-0
           {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="feather icon-users me-2"></i> My Team
            </a>
        @endif

        {{-- Notifications (all roles) --}}
        <a href="{{ route('notifications.index') }}"
            class="list-group-item list-group-item-action border-0
           {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="feather icon-bell me-2"></i> Notifications
        </a>

    </section>
</aside>

{{-- Sidebar overlay (mobile) --}}
<div class="d-none position-fixed top-0 left-0 w-100 h-100" id="sidebar-overlay"
    style="background:rgb(0 0 0/50%);z-index:99;"></div>
