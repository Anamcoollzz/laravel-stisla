@extends('spark-admin.layouts.app-spark-admin')

@section('title', 'Blank Page - Spark Admin Premium Bootstrap 5 Admin Dashboard Template')

@section('page-header')
    <div>
        <h1 class="page-title">Blank Page</h1>
        <p class="page-subtitle">A blank starter template page for custom extensions.</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pages.spark-admin.index') }}" class="text-decoration-none text-muted-green">Home</a></li>
            <li class="breadcrumb-item active text-main" aria-current="page">Blank Page</li>
        </ol>
    </nav>
@endsection

@section('content')
    <!-- START: Blank Page Content Area -->
    <div class="card p-4 border-light shadow-sm text-center">
        <div>
            <div class="mb-4 text-lime empty-state-icon">
                <i class="bi bi-file-earmark-fill"></i>
            </div>
            <h3 class="mb-2">Your Content Starts Here</h3>
            <p class="text-muted-green mb-4">Use this blank page starter template to build custom dashboard views, tables,
                forms, or any modules required for your Spark Admin system.</p>
        </div>
    </div>
    <!-- END: Blank Page Content Area -->
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('modules/admin/spark-admin/assets/libs/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/admin/spark-admin/assets/libs/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/admin/spark-admin/assets/libs/apexcharts/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/admin/spark-admin/assets/libs/flatpickr/flatpickr.min.css') }}">
@endsection

@section('js')
    <script src="{{ asset('modules/admin/spark-admin/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('modules/admin/spark-admin/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('modules/admin/spark-admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
@endsection
