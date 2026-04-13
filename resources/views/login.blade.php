@extends('layouts.app')
@section('content')

<section class="d-flex align-items-center min-vh-100 bg-light">
  <div class="container-fluid px-4">
    <div class="row no-gutter justify-content-center">
      <div class="col-md-10 col-lg-8 shadow rounded overflow-hidden">
        <div class="row">
          <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-white border-end">
            <article class="text-center text-white">
              <img src="{{ asset('assets/img/newgenguru.png') }}" class="img-fluid" alt="Sangeeta Steel" width="240">
            </article>
          </div>
          <div class="col-lg-6 bg-white">
            <section class="px-3 py-4">
              <img class="d-block mx-auto my-5" src="{{ asset('assets/img/newgenguru.png') }}" width="160" alt="newgenguru">
              <h5 class="mb-3 text-primary font-weight-bold">Log In</h5>

              @error('message')
              <article class="alert alert-danger alert-dismissible fade show shadow-sm">
                <small>{{ $message }}</small>
                <button class="btn-close small" data-bs-dismiss="alert"></button>
              </article>
              @enderror

              {{-- login --}}
              <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="mb-3">
                  <input type="email" class="form-control py-3" name="email" placeholder="Email Address" value="{{ old('email') }}" required>
                  @error('email')
                  <div class="text-danger mt-1">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <input type="password" class="form-control py-3" name="password" placeholder="Password" required>
                  @error('password')
                  <div class="text-danger mt-1">{{ $message }}</div>
                  @enderror
                </div>

                <section class="d-flex justify-content-between mb-3">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label text-muted" for="remember">Remember me</label>
                  </div>
                  <a href="#">Forget Password?</a>
                </section>
                <button type="submit" class="btn w-100 btn-primary py-2 mb-3">Login</button>
              </form>
            </section>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
