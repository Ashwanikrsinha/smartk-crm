@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>New News</h5>
    <a href="{{ route('news.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('news.store') }}" method="POST" class="p-3 shadow-sm rounded bg-white">
    @include('news.form', ['mode' => 'create'])
</form>
@endsection

