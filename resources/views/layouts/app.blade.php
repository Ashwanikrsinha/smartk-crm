<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ config('app.name') }}</title>
  <link rel="icon" type="image/png" href="{{ asset('assets/img/newgenguru-icon.png') }}">

  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
      :root{ --bs-font-sans-serif: "Rubik", system-ui; }
      
      .form-control:focus{ border-color: var(--bs-primary);}
  </style>
</head>

<body>
 <main>
    @yield('content')
  </main>
  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  @stack('scripts')
</body>
</html>
