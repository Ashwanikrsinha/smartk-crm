@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between mb-4 align-companys-center">
    <h5>Edit Company</h5>
    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary">Back</a>
</header>

<form action="{{ route('companies.update', ['company' => $company ]) }}" method="POST" 
    class="p-3 shadow-sm rounded bg-white"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <section class="row">
        <div class="col-lg-6 mb-3">
            <label for="" class="form-label">Name</label>
            <input type="text" class="form-control" name="name" value="{{ $company->name }}" required>
        </div>

        <div class="col-lg-6 mb-3">
            <label for="" class="form-label">Phone No.</label>
            <input type="text" class="form-control" name="phone_number" value="{{ $company->phone_number }}" required>
        </div>

        <div class="col-lg-6 mb-3">
            <label for="" class="form-label">GST No.</label>
            <input type="text" class="form-control" name="gst_number" value="{{ $company->gst_number }}">
        </div>

        <div class="col-lg-6 mb-3">
            <label for="" class="form-label">Email Address</label>
            <input type="text" class="form-control" name="email" value="{{ $company->email }}">
        </div>

        <div class="col-lg-6 mb-3">
            <label for="" class="form-label">Logo</label>
            <input type="file" class="form-control" name="logo">
        </div>

        <div class="col-lg-6 mb-3">
            <label for="" class="form-label">Address</label>
            <input type="text" class="form-control" name="address" value="{{ $company->address }}" required>
        </div>
        
        @if (isset($company->logo)) 
            <div class="col-lg-6">
                <a href="{{ url('storage/'.$company->logo) }}" target="_blank">
                    <img src="{{ url('storage/'.$company->logo) }}" alt="{{ $company->logo }}" 
                    class="mb-4 border rounded p-2"
                    style="width:12rem; height: 4rem; object-fit:contain;">
               </a>
            </div>
        @endif

    </section>

    <div class="mb-3">
        <label for="" class="form-label">Terms & Conditions</label>
        <textarea class="form-control" name="terms"  cols="5" rows="5">{{ $company->terms }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary">Edit</button>
</form>

@endsection


@push('scripts')
    <script>
      
      $(document).ready(()=>{
        
        tinymce.init({
            selector: '[name=terms]',
            height: 420,
            branding: false,
            plugins: 'lists link image paste table fullscreen',
            toolbar: `undo redo | bold italic underline | alignleft
                    aligncenter alignright alignjustify | bullist numlist outdent indent 
                    | table |link image | fullscreen`,
        });

        
      });

    </script>
@endpush