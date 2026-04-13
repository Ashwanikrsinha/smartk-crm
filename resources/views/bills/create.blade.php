@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>New {{ ucwords($type) }} Bill</h5>
    <a href="{{ route('bills.index', ['type' => $type]) }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('bills.store') }}" method="POST" 
class="p-3 shadow-sm rounded bg-white mb-5"
onsubmit="return confirm('Are You Sure?')">
    @include('bills.form', ['mode' => 'create'])
</form>
@endsection

