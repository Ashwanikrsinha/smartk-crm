@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>Edit Visit</h5>
    <a href="{{ route('visits.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('visits.update', ['visit' => $visit]) }}" method="POST" class="p-3 shadow-sm rounded bg-white mb-4">
    @method('PUT')
    @include('visits.form', ['mode' => 'edit'])
</form>

@include('customers.modal')
 
@if(isset($visit) && $visit->attachments->count() > 0)
<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3">
        <i class="feather icon-file me-1 bg-primary text-white rounded p-1"></i> Attachments
    </header>
    <div class="card-body">       
        @foreach ($visit->attachments as $attachment)
            <form action="{{ route('visits.attachments.destroy', ['attachment' => $attachment ]) }}" method="POST"
                onsubmit="return confirm('Are You Sure?')" class="d-lg-flex mb-3">
                @csrf
                @method('DELETE')
                <a href="{{ url('storage/'.$attachment->filename) }}" target="_blank" class="me-2">
                    <i class="feather icon-file me-1 fs-5"></i> 
                    <span class="text-muted">{{ $attachment->filename }}</span>
                </a>
                <button type="submit" class="btn btn-sm p-0">
                    <i class="feather icon-x"></i>
                </button>
            </form>
        @endforeach
    </div>
</div>
@endif

@endsection
