<form action="{{ $route }}" class="d-flex align-items-center mt-3 mt-md-0" method="GET">
    <input type="search" name="search" class="form-control mr-1 search" placeholder="Search..." 
    value="{{ request('search') ?? '' }}">
    <button class="btn btn-primary ms-1">
    	<i class="fa fa-search"></i>
    </button>
</form>
