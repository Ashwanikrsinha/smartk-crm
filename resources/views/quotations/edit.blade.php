@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>Edit Quotation - {{ $quotation->quotation_number }}</h5>
    <div> 
     @isset($visit)
     <a href="{{ route('quotations.edit', ['quotation' => $quotation]) }}"  class="btn btn-sm btn-primary me-1">Undo</a>
     @endisset
     <a href="{{ route('quotations.index') }}"  class="btn btn-sm btn-secondary">Back</a>
    </div>
</header>

<form action="{{ route('quotations.update', ['quotation' => $quotation]) }}" method="POST" class="p-3 shadow-sm rounded bg-white">
    @method('PUT')
    @include('quotations.form', ['mode' => 'edit'])
</form>


@if(isset($quotation) && $quotation->attachments->count() > 0)
    <div class="bg-white shadow-sm rounded p-3 my-4">
    <h6 class="mb-4 fw-bold">Attachments</h6>
        @foreach ($quotation->attachments as $attachment)
            <form action="{{ route('quotations.attachments.destroy', ['attachment' => $attachment ]) }}" method="POST"
                onsubmit="return confirm('Are You Sure?')" class="d-lg-flex mb-3">
                @csrf
                @method('DELETE')
                <a href="{{ url('storage/'.$attachment->filename) }}" target="_blank" class="me-2">
                    <i class="feather icon-file me-1 text-success fs-5"></i> 
                    <span class="text-muted">{{ $attachment->filename }}</span>
                </a>
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="feather icon-x"></i>
                </button>
            </form>
        @endforeach
    </div>
@endif

@endsection
