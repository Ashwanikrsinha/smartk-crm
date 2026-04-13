@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-items-center">
    <h5>Edit Product</h5>
    <a href="{{ route('products.index') }}"  class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('products.update', ['product' => $product ]) }}" method="POST"
    class="p-3 shadow-sm rounded bg-white">
    @method('PUT')
    @include('products.form', ['mode' => 'edit'])

</form>


@if(isset($product) && $product->images->count() > 0)
 <section class="card border-0 my-4 bg-white shadow-sm">
    <header class="card-header bg-white py-3">
      <i class="feather icon-image me-1 bg-primary text-white p-1 rounded"></i> Images
    </header>
    <div class="card-body">
        <div class="row">
            @foreach ($product->images as $image)
                <div class="col-lg-4 col-xl-3">
                <form action="{{ route('products.images.destroy', ['image' => $image ]) }}" method="POST"
                    onsubmit="return confirm('Are You Sure?')" class="text-end mb-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-primary p-1">
                        <i class="feather icon-x"></i>
                    </button>
                </form>
                 <img src="{{ url('storage/'.$image->filename) }}" alt="{{ $image->filename }}"
                 class="shadow-sm mb-3" style="object-fit: cover; height:14rem; width:100%;">
                </div>
            @endforeach
        </div>
    </div>
 </section>
@endif

@endsection