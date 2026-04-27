@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5>Edit Target</h5>
            <small class="text-muted">{{ $target->user->username }} — {{ $target->month_label }}</small>
        </div>
        <a href="{{ route('targets.index') }}" class="btn btn-sm btn-secondary">Back</a>
    </header>

    <form action="{{ route('targets.update', $target) }}" method="POST" class="bg-white rounded shadow-sm p-4">
        @method('PUT')
        @include('targets.form')
    </form>
@endsection
