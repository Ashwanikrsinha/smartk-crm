@extends('layouts.dashboard')
@section('content')
    <header class="mb-4">
        <h5>Dashboard</h5>
        <small class="text-muted">Welcome, {{ auth()->user()->username }}</small>
    </header>

    {{-- Warehouse role sees dispatch queue shortcut --}}
    @if (auth()->user()->hasPermission('browse_dispatch_queue'))
        <div class="row g-3">
            <div class="col-lg-4">
                <div class="bg-white rounded shadow-sm p-4 text-center">
                    <i class="feather icon-truck fs-2 text-primary mb-2 d-block"></i>
                    <h6>Dispatch Queue</h6>
                    <p class="text-muted small">Approved orders pending dispatch</p>
                    <a href="{{ route('pdcs.index') }}" class="btn btn-primary btn-sm">View Queue</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="bg-white rounded shadow-sm p-4 text-center">
                    <i class="feather icon-archive fs-2 text-warning mb-2 d-block"></i>
                    <h6>Inventory</h6>
                    <p class="text-muted small">Check current stock levels</p>
                    <a href="#" class="btn btn-outline-warning btn-sm">View Inventory</a>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded shadow-sm p-4 text-center" style="max-width:400px; margin:0 auto;">
            <i class="feather icon-home fs-1 text-muted mb-3 d-block"></i>
            <h6>You're logged in as <strong>{{ auth()->user()->role->name }}</strong></h6>
            <p class="text-muted small">Use the sidebar to navigate to your available modules.</p>
        </div>
    @endif
@endsection
