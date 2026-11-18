<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Autentikasi') | OLIMFY</title>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    {{-- AdminLTE 4 (sudah termasuk Bootstrap 5) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.10.2/dist/css/adminlte.min.css">

    @stack('styles')
</head>
<body class="login-page bg-body-secondary">
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/') }}"><b>OLIM</b>FY</a>
        </div>

        <div class="card card-outline card-primary">
            <div class="card-body login-card-body">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    {{-- Bootstrap 5: Popper sudah termasuk dalam bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    {{-- AdminLTE 4 --}}
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.10.2/dist/js/adminlte.min.js"></script>

    @stack('scripts')
</body>
</html>
