@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Settings</h5>
  <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">Back</a>
</header>

<h6 class="text-muted border-bottom pb-2 mb-4">Products</h6>

<section class="row">

    @can('viewAny', App\Models\Unit::class)
        <div class="col-md-2 col-lg-3">
            <a href="{{ route('units.index') }}">
                <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                    <i class="feather icon-archive text-white p-1 rounded bg-primary"></i>
                    <h6 class="d-inline-block ms-2 mb-0 text-body">Units</h6>
                </article>
            </a>
        </div>
    @endcan

    @can('viewAny', App\Models\Group::class)
        <div class="col-md-2 col-lg-3">
            <a href="{{ route('groups.index') }}">
                <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                    <i class="feather icon-archive text-white p-1 rounded bg-success"></i>
                    <h6 class="d-inline-block ms-2 mb-0 text-body">Groups</h6>
                </article>
            </a>
        </div>
    @endcan

    @can('viewAny', App\Models\Category::class)
        <div class="col-md-2 col-lg-3">
            <a href="{{ route('categories.index') }}">
                <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                    <i class="feather icon-archive text-white p-1 rounded bg-danger"></i>
                    <h6 class="d-inline-block ms-2 mb-0 text-body">Categories</h6>
                </article>
            </a>
        </div>
    @endcan

    
</section>


<h6 class="text-muted border-bottom pb-2 mb-4">Contacts</h6>

<section class="row">

    @can('viewAny', App\Models\Designation::class)
        <div class="col-md-2 col-lg-3">
            <a href="{{ route('designations.index') }}">
                <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                    <i class="feather icon-layers text-white p-1 rounded bg-primary"></i>
                    <h6 class="d-inline-block ms-2 mb-0 text-body">Designations</h6>
                </article>
            </a>
        </div>
    @endcan


    @can('viewAny', App\Models\Segment::class)
        <div class="col-md-2 col-lg-3">
            <a href="{{ route('segments.index') }}">
                <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                    <i class="feather icon-archive text-white p-1 rounded bg-success"></i>
                    <h6 class="d-inline-block ms-2 mb-0 text-body">Segments</h6>
                </article>
            </a>
        </div>
    @endcan


    @can('viewAny', App\Models\CustomerType::class)
        <div class="col-md-2 col-lg-3">
            <a href="{{ route('customer-types.index') }}">
                <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                    <i class="feather icon-archive text-white p-1 rounded bg-danger"></i>
                    <h6 class="d-inline-block ms-2 mb-0 text-body">Customer Types</h6>
                </article>
            </a>
        </div>
    @endcan

    @can('viewAny', App\Models\SupplierType::class)
        <div class="col-md-2 col-lg-3">
            <a href="{{ route('supplier-types.index') }}">
                <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                    <i class="feather icon-grid text-white p-1 rounded bg-warning"></i>
                    <h6 class="d-inline-block ms-2 mb-0 text-body">Supplier Types</h6>
                </article>
            </a>
        </div>
    @endcan


</section>



<h6 class="text-muted border-bottom pb-2 mb-4">Extra</h6>

<section class="row">


    @can('viewAny', App\Models\Department::class)
    <div class="col-md-2 col-lg-3">
        <a href="{{ route('departments.index') }}">
            <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                <i class="feather icon-tag text-white p-1 rounded bg-primary"></i>
                <h6 class="d-inline-block ms-2 mb-0 text-body">Departments</h6>
            </article>
        </a>
    </div>
    @endcan

    <div class="col-md-2 col-lg-3">
        <a href="#">
            <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                <i class="feather icon-map text-white p-1 rounded bg-success"></i>
                <h6 class="d-inline-block ms-2 mb-0 text-body">Locations</h6>
            </article>
        </a>
    </div>

    @can('viewAny', App\Models\Transport::class)
    <div class="col-md-2 col-lg-3">
        <a href="{{ route('transports.index') }}">
            <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                <i class="feather icon-truck text-white p-1 rounded bg-danger"></i>
                <h6 class="d-inline-block ms-2 mb-0 text-body">Transports</h6>
            </article>
        </a>
    </div>
    @endcan


</section>



<h6 class="text-muted border-bottom pb-2 mb-4">Roles & Permissions</h6>

<section class="row">
 
    
    @can('viewAny', App\Models\Permission::class)
    <div class="col-md-2 col-lg-3">
        <a href="{{ route('permissions.index') }}">
            <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                <i class="feather icon-unlock text-white p-1 rounded bg-primary"></i>
                <h6 class="d-inline-block ms-2 mb-0 text-body">Permissions</h6>
            </article>
        </a>
    </div>
    @endcan

    @can('viewAny', App\Models\Role::class)
    <div class="col-md-2 col-lg-3">
        <a href="{{ route('roles.index') }}">
            <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                <i class="feather icon-user-plus text-white p-1 rounded bg-success"></i>
                <h6 class="d-inline-block ms-2 mb-0 text-body">Roles</h6>
            </article>
        </a>
    </div>
    @endcan

</section>




<h6 class="text-muted border-bottom pb-2 mb-4">CRM</h6>

<section class="row">
 
    
    @can('viewAny', App\Models\Event::class)
    <div class="col-md-2 col-lg-3">
        <a href="{{ route('events.index') }}">
            <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                <i class="feather icon-bookmark text-white p-1 rounded bg-primary"></i>
                <h6 class="d-inline-block ms-2 mb-0 text-body">Events</h6>
            </article>
        </a>
    </div>
    @endcan

    @can('viewAny', App\Models\Purpose::class)
    <div class="col-md-2 col-lg-3">
        <a href="{{ route('purposes.index') }}">
            <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                <i class="feather icon-tag text-white p-1 rounded bg-success"></i>
                <h6 class="d-inline-block ms-2 mb-0 text-body">Purposes</h6>
            </article>
        </a>
    </div>
    @endcan

</section>

@endsection