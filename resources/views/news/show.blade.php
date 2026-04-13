@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
    <h5>News</h5>
    <a href="{{ route('news.index') }}" class="btn btn-sm btn-secondary">Back</a>
</header>


<div class="card shadow-sm border-0 mb-4">
    <header class="card-header bg-white py-3 d-flex align-items-center">
        <i class="feather icon-message-circle me-1 bg-primary text-white rounded p-1"></i>
        <small class="text-secondary mx-1">{{ $news->published_at->format('d M, Y') }}</small>
        &bull;
        <span class="badge alert-primary ms-1 rounded-pill">{{$news->event->name }}</span>
    </header>
    <div class="card-body">
              
        <h5 class="mb-4">{{ $news->title }}</h5>

        @if($news->images->count() > 0)
        <div class="row">
            @foreach ($news->images as $image)
            <div class="col-lg-6 col-xl-4">
                <a href="{{ url('storage/'.$image->filename) }}" target="_blank">
                    <img src="{{ url('storage/'.$image->filename) }}" alt="{{ $image->filename }}"
                        class="shadow-sm rounded mb-3" style="object-fit: cover; height:14rem; width:100%;">
                </a>
            </div>
            @endforeach
        </div>
        @endif

        <div class="mb-4">{!! $news->description !!}</div>

    </div>
</div>  

@endsection