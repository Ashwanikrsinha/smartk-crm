@extends('layouts.dashboard')
@section('content')

<header class="d-flex justify-content-between align-items-center mb-4">
  <h5>Edit User Profile</h5>
  <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">Back</a>
</header>

<form action="{{ route('users.profile.update', ['user' => auth()->id() ]) }}" method="POST"
  onsubmit="return confirm('Are you sure?');" class="bg-white p-3 rounded">
  @csrf
  @method('PUT')

  <section class="row">


    <div class="col-lg-6 mb-3">
      <label for="" class="form-label">Username</label>
      <input type="text" class="form-control" name="username" value="{{ $user->username ?? old('username') }}" required>
    </div>

    <div class="col-lg-6 mb-3">
      <label for="" class="form-label">Email Address</label>
      <input type="email" class="form-control" name="email" value="{{ $user->email ?? old('email') }}" required>
    </div>

    <div class="col-lg-3 mb-3">
      <label for="" class="form-label">Role</label>
      <div class="form-control">{{ $user->role->name }}</div>
    </div>

    <div class="col-lg-3 mb-3">
      <label for="" class="form-label">Department</label>
      <div class="form-control">{{ $user->department->name }}</div>
    </div>


  
    <div class="col-lg-6 mb-3">
      <label for="" class="form-label">Reportive To</label>
      <div class="form-control">{{ isset($user->reportive_id) ? $user->reportiveTo->name : 'NOT GIVEN' }}</div>
    </div>


  </section>

  <section class="row">
    <div class="col-lg-6 mb-3">
      <label for="password" class="form-label">Password (Optional)</label>
      <input type="password" name="password" id="password" class="form-control" autocomplete="off">
    </div>

    <div class="col-lg-6 mb-3">
      <label for="password_confirmation" class="form-label">Confirm Password</label>
      <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
        autocomplete="off">
    </div>
  </section>

  <button type="submit" class="btn btn-primary">Edit</button>
</form>

@endsection


@push('scripts')

<script>
  $(document).ready(()=>{

      $('select').selectize();

      tinymce.init({
            selector: '[name=qualification]',
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