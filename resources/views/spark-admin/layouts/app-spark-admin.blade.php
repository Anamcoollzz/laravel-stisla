<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <!-- SEO Optimization -->
    @include('spark-admin.partials.meta')

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('modules/admin/spark-admin/assets/images/favicon.ico') }}">

    <!-- Local Third-Party Libraries (100% Offline Compatible) -->
    @yield('css')

    <!-- Main Design System & Custom Stylesheet -->
    <link rel="stylesheet" href="{{ asset('modules/admin/spark-admin/assets/css/main.css') }}">
</head>

<body>

    @include('spark-admin.partials.sidebar')
    <!-- ==========================================
         START: Main Content Area
         ========================================== -->
    <div class="main-wrapper">

        <!-- START: Top Navbar Component -->
        @include('spark-admin.partials.header')
        <!-- END: Top Navbar Component -->

        <!-- START: Dashboard Header Banner -->
        <div class="page-header">
            @yield('page-header')
        </div>
        <!-- END: Dashboard Header Banner -->

        <!-- START: Main Layout Grid (2 Columns: Dashboard + Performance Pane) -->
        @yield('content')
        <!-- END: Main Layout Grid -->

        <!-- START: Footer Component -->
        @include('spark-admin.partials.footer')
        <!-- END: Footer Component -->

    </div>
    <!-- ==========================================
         END: Main Content Area
         ========================================== -->

    <!-- Local Third-Party Libraries Script dependencies -->
    @yield('js')

    <!-- Local dashboard interactions controller -->
    <script src="{{ asset('modules/admin/spark-admin/assets/js/dashboard.js') }}"></script>
</body>

</html>