<header class="d-flex justify-content-between align-items-center shadow-sm px-md-5 p-3 bg-white sticky-top d-print-none"
style="z-index: 90;">
    <!-- sidebar opener -->
    <button class="btn text-primary d-md-none py-0" id="sidebar-opener">
        <span class="icon feather icon-chevron-right fs-3"></span>
    </button>

    <h6 class="text-primary d-none d-md-block">
        <i class="feather icon-grid me-2"></i> 
        {{ config('app.name') }}
    </h6>

    <div class="d-flex justify-content-between align-items-center">

        {{-- off canvas opener --}}
        <button class="btn btn-transparent p-0 position-relative me-2 me-lg-3" 
            data-bs-toggle="offcanvas" data-bs-target="#off-canvas-right" aria-controls="off-canvas-right">
            <span class="p-1 rounded d-inline-block bg-danger position-absolute top-0 end-0"></span>
            <i class="feather icon-bell text-primary"></i>
        </button>

        <div class="dropdown">
            <button class="btn d-flex align-items-center py-0" type="button" id="logout-dropdown" data-bs-toggle="dropdown">
            <small class="mb-1 pe-2 text-muted">{{ Str::limit(auth()->user()->username, '15') }}</small>
                <span class="feather icon-user bg-primary p-1 text-white rounded"></span>
                <span class="feather icon-chevron-down small ms-2 mb-1"></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="logout-dropdown">
                <a href="{{ route('users.profile.show', ['user' => auth()->id() ]) }}" class="dropdown-item">Profile</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item">Logout</button>
                </form>
            </div>
        </div>
    </div>    
     
</header>


<div class="offcanvas offcanvas-end" tabindex="-1" id="off-canvas-right">
  <div class="offcanvas-header border-bottom">
    <h5 class="mb-0">Notifications Overview</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">

    <ul class="nav justify-content-center border rounded px-2 py-1 mb-3" id="pills-tab" role="tablist">
        <li class="nav-item w-50" role="presentation">
          <button class="badge bg-primary w-100 active border-0 py-2" data-bs-toggle="pill" data-bs-target="#assigned-tasks" type="button" role="tab">Assigned Tasks</button>
        </li>
        <li class="nav-item w-50" role="presentation">
          <button class="badge bg-success w-100 ms-1 border-0 py-2" data-bs-toggle="pill" data-bs-target="#self-tasks" type="button" role="tab">Self Tasks</button>
        </li>
      </ul>

      <div class="tab-content">

        <div class="tab-pane fade show active" id="assigned-tasks" role="tabpanel">
            <article class="bg-success text-white rounded px-3 py-2 mb-3">
               <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1">15</h5>
                    <h6 class="mb-0">Completed</h6>
                </div>
                <i class="feather icon-check-circle fs-4"></i>
               </div>
            </article>

            <article class="bg-warning text-white rounded px-3 py-2 mb-3">
                <div class="d-flex align-items-center justify-content-between">
                 <div>
                     <h5 class="mb-1">12</h5>
                     <h6 class="mb-0">Pending</h6>
                 </div>
                 <i class="feather icon-check-circle fs-4"></i>
                </div>
             </article>


             <article class="alert-primary rounded px-3 py-2 mb-3">
                <div class="d-flex align-items-center justify-content-between">
                 <div>
                     <h5 class="mb-1">5</h5>
                     <h6 class="mb-0">Employee Leaves</h6>
                 </div>
                 <i class="feather icon-check-circle fs-4"></i>
                </div>
             </article>
        </div>

        <div class="tab-pane fade" id="self-tasks" role="tabpanel">
            <article class="bg-success text-white rounded px-3 py-2 mb-3">
                <div class="d-flex align-items-center justify-content-between">
                 <div>
                     <h5 class="mb-1">15</h5>
                     <h6 class="mb-0">Completed</h6>
                 </div>
                 <i class="feather icon-check-circle fs-4"></i>
                </div>
             </article>
 
             <article class="bg-warning text-white rounded px-3 py-2 mb-3">
                 <div class="d-flex align-items-center justify-content-between">
                  <div>
                      <h5 class="mb-1">12</h5>
                      <h6 class="mb-0">Pending</h6>
                  </div>
                  <i class="feather icon-check-circle fs-4"></i>
                 </div>
              </article>
 
 
        </div>

      </div>

  </div>
</div>