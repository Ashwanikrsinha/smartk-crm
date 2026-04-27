<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - {{ $company->name }}</title>
    <link rel="icon" type="image/png" href="{{ url('storage/'.$company->logo) ?? asset('assets/img/newgenguru-icon.png') }}">


    <!-- styles -->
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/filepond/4.30.3/filepond.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.css">


    <!-- fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/feather-font/css/iconfont.css') }}" rel="stylesheet">

    <style>
        :root{ --bs-font-sans-serif: 'Rubik', system-ui; }

        input[readonly]{
            background-color: #ffffff !important;
        }

        .table>:not(:last-child)>:last-child>*{
            border-bottom-color: inherit;
        }

        .dataTables_wrapper > div:first-child,
        .dataTables_wrapper > div:last-child{
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .page-link{ border : none; }
        .page-item.active .page-link{ border-radius: 0.25rem; }

        .feather{ font-size: 1rem; }

        .form-control:focus{ border-color: var(--bs-primary);}

        .badge{ letter-spacing: 0.025rem; }

        .flatpickr-calendar.inline{
            box-shadow: none;
        }

        .flatpickr-day.today {
            background: var(--bs-primary);
            color: #fff;
        }

        .flatpickr-day{
            border-radius: 0.25rem;
            border: unset;
        }

        .tooltip-inner{
            min-width: 14rem;
            padding: 1rem;
         }

        .page-link{ background-color: inherit; }

        .offcanvas-end{ width: 320px; }

    </style>
</head>

<body>

    <div id="spinner-container" class="w-100 vh-100 justify-content-center
        align-items-center bg-white position-fixed" style="display:flex; z-index: 999;">
        <img src="{{ url('storage/'.$company->logo) ?? asset('assets/img/newgenguru.png') }}" alt="newgenguru" width="180">
    </div>

    @include('partials/sidebar')

    <div class="col-md-9 col-xl-10 px-0 ms-md-auto">

        @include('partials/top-nav')

        <main class="p-4 bg-light min-vh-100">
            @include('partials/success')
            @include('partials/errors')

            @yield('content')
        </main>
    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.9.2/tinymce.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.9.2/plugins/paste/plugin.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/filepond/4.30.3/filepond.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>

    <script src="{{ asset('assets/js/main.js') }}"></script>

    <script>

        $(document).ready(()=>{
            $('#spinner-container').fadeOut(1500);
            setTimeout(() => { $('#success').remove(); }, 5000);
        });


        Chart.defaults.font.size = '14';
        Chart.defaults.font.family = 'Rubik';

    </script>
    @stack('scripts')
</body>
</html>
