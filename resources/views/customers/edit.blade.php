@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>Edit Customer</h5>
    <a href="{{ route('customers.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('customers.update', ['customer' => $customer]) }}" method="POST" 
    class="p-3 shadow-sm rounded bg-white">
    @method('PUT')
    @include('customers.form', ['mode' => 'edit'])
</form>
@endsection



