@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>Edit {{ ucwords($bill->type) }} Bill - {{ $bill->bill_number }}</h5>
    <a href="{{ route('bills.index', ['type' => $bill->type]) }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('bills.update', ['bill' => $bill]) }}" method="POST" 
    class="p-3 shadow-sm rounded bg-white mb-5"
    onsubmit="return confirm('Are You Sure?')">
    @method('PUT')
    @include('bills.form', ['mode' => 'edit'])
</form>


@if(isset($bill) && $bill->attachments->count() > 0)
    <div class="bg-white shadow-sm rounded p-3 my-4">
    <h6 class="mb-4 fw-bold">Attachments</h6>
        @foreach ($bill->attachments as $attachment)
            <form action="{{ route('bills.attachments.destroy', ['attachment' => $attachment ]) }}" method="POST"
                onsubmit="return confirm('Are You Sure?')" class="d-lg-flex mb-3">
                @csrf
                @method('DELETE')
                <a href="{{ url('storage/'.$attachment->filename) }}" target="_blank" class="me-2">
                    <i class="feather icon-file me-1 text-success fs-5"></i> 
                    <span class="text-muted">{{ $attachment->filename }}</span>
                </a>
                <button type="submit" class="btn btn-danger p-0">
                    <i class="feather icon-x"></i>
                </button>
            </form>
        @endforeach
    </div>
@endif

@endsection
