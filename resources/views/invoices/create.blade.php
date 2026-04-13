@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>New Performa Invoice</h5>
    <a href="{{ route('invoices.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('invoices.store') }}" method="POST" 
class="p-3 shadow-sm rounded bg-white mb-5"
onsubmit="return confirm('Are You Sure?')">
    @include('invoices.form', ['mode' => 'create'])
</form>
@endsection

