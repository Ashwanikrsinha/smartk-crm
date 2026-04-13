@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>New Product</h5>
    <a href="{{ route('products.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('products.store') }}" method="POST" class="p-3 shadow-sm rounded bg-white">
    @include('products.form', ['mode' => 'create'])
</form>

@endsection