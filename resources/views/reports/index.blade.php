@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Reports</h5>
  <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">Back</a>
</header>

<h6 class="text-muted border-bottom pb-2 mb-4">Visits</h6>

<section class="row">


    <div class="col-md-2 col-lg-3">
        <a href="{{ route('visits.aging') }}">
            <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
                <i class="feather icon-book-open text-white p-1 rounded bg-primary"></i>
                <h6 class="d-inline-block ms-2 mb-0 text-body">Visit Aging</h6>
            </article>
        </a>
    </div>

    
    <div class="col-md-2 col-lg-3">
        <a href="{{ route('targets.index') }}">
        <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
            <i class="feather icon-target text-white p-1 rounded bg-success"></i>
            <h6 class="d-inline-block ms-2 mb-0 text-body">Targets</h6>
        </article>
        </a>
    </div>

    
    <div class="col-md-2 col-lg-3">
        <a href="#">
        <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
            <i class="feather icon-file-text text-white p-1 rounded bg-danger"></i>
            <h6 class="d-inline-block ms-2 mb-0">Report 3</h6>
        </article>
        </a>
    </div>

    
    <div class="col-md-2 col-lg-3">
        <a href="#">
        <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
            <i class="feather icon-file-text text-white p-1 rounded bg-warning"></i>
            <h6 class="d-inline-block ms-2 mb-0">Report 4</h6>
        </article>
        </a>
    </div>

</section>


<h6 class="text-muted border-bottom pb-2 mb-4">Sales & Purchase</h6>

<section class="row">


    <div class="col-md-2 col-lg-3">
        <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
            <i class="feather icon-file-text text-white p-1 rounded bg-primary"></i>
            <h6 class="d-inline-block ms-2 mb-0">Report 1</h6>
        </article>
    </div>

    
    <div class="col-md-2 col-lg-3">
        <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
            <i class="feather icon-target text-white p-1 rounded bg-success"></i>
            <h6 class="d-inline-block ms-2 mb-0">Report 2</h6>
        </article>
    </div>

    
    <div class="col-md-2 col-lg-3">
        <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
            <i class="feather icon-book text-white p-1 rounded bg-danger"></i>
            <h6 class="d-inline-block ms-2 mb-0">Report 3</h6>
        </article>
    </div>


</section>



<h6 class="text-muted border-bottom pb-2 mb-4">Tally</h6>

<section class="row">


    <div class="col-md-2 col-lg-3">
        <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
            <i class="feather icon-file-text text-white p-1 rounded bg-primary"></i>
            <h6 class="d-inline-block ms-2 mb-0">Report 1</h6>
        </article>
    </div>

    
    <div class="col-md-2 col-lg-3">
        <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
            <i class="feather icon-message-square text-white p-1 rounded bg-success"></i>
            <h6 class="d-inline-block ms-2 mb-0">Report 2</h6>
        </article>
    </div>

    
    <div class="col-md-2 col-lg-3">
        <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
            <i class="feather icon-calendar text-white p-1 rounded bg-danger"></i>
            <h6 class="d-inline-block ms-2 mb-0">Report 3</h6>
        </article>
    </div>

    
    <div class="col-md-2 col-lg-3">
        <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
            <i class="feather icon-file-text text-white p-1 rounded bg-warning"></i>
            <h6 class="d-inline-block ms-2 mb-0">Report 4</h6>
        </article>
    </div>

    
    <div class="col-md-2 col-lg-3">
        <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
            <i class="feather icon-message-circle text-white p-1 rounded bg-info"></i>
            <h6 class="d-inline-block ms-2 mb-0">Report 5</h6>
        </article>
    </div>

</section>



<h6 class="text-muted border-bottom pb-2 mb-4">Accounts</h6>

<section class="row">
 
    <div class="col-md-2 col-lg-3">
        <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
            <i class="feather icon-message-square text-white p-1 rounded bg-success"></i>
            <h6 class="d-inline-block ms-2 mb-0">Report 2</h6>
        </article>
    </div>

    
    <div class="col-md-2 col-lg-3">
        <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
            <i class="feather icon-calendar text-white p-1 rounded bg-danger"></i>
            <h6 class="d-inline-block ms-2 mb-0">Report 3</h6>
        </article>
    </div>

    
    <div class="col-md-2 col-lg-3">
        <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
            <i class="feather icon-file-text text-white p-1 rounded bg-warning"></i>
            <h6 class="d-inline-block ms-2 mb-0">Report 4</h6>
        </article>
    </div>

    
    <div class="col-md-2 col-lg-3">
        <article class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-4">
            <i class="feather icon-message-circle text-white p-1 rounded bg-info"></i>
            <h6 class="d-inline-block ms-2 mb-0">Report 5</h6>
        </article>
    </div>

</section>

@endsection