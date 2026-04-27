@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h5>Set Target</h5>
        <a href="{{ route('targets.index') }}" class="btn btn-sm btn-secondary">Back</a>
    </header>

    <form action="{{ route('targets.store') }}" method="POST" class="bg-white rounded shadow-sm p-4">
        @include('targets.form')
    </form>
@endsection
