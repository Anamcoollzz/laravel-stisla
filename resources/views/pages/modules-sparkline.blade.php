@extends('layouts.app')

@section('title', 'Modules &rsaquo; Sparkline')

@push('js')
  <script src="{{ asset('node_modules/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
@endpush

@push('page-js')
  <script src="{{ asset('assets/js/page/modules-sparkline.js') }}"></script>
@endpush

@section('content')
  <div class="section-header">
    <h1>Sparkline</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
      <div class="breadcrumb-item"><a href="#">Modules</a></div>
      <div class="breadcrumb-item">Sparkline</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">Sparkline</h2>
    <p class="section-lead">
      We use 'Sparkline' made by 'Gareth Watts'. You can check the full documentation <a href="https://omnipotent.net/jquery.sparkline/">here</a>.
    </p>

    <div class="row">
      <div class="col-12 col-md-6 col-lg-6">
        <div class="card">
          <div class="card-header">
            <h4>Line Chart</h4>
          </div>
          <div class="card-body">
            <div class="sparkline-inline"></div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-6">
        <div class="card">
          <div class="card-header">
            <h4>Bar Chart</h4>
          </div>
          <div class="card-body">
            <div class="sparkline-bar"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-md-6 col-lg-6">
        <div class="card">
          <div class="card-header">
            <h4>Line Chart #2</h4>
          </div>
          <div class="card-body">
            <div class="sparkline-line"></div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-6">
        <div class="card">
          <div class="card-header">
            <h4>Pie Chart</h4>
          </div>
          <div class="card-body">
            <div class="text-center">
              <div class="sparkline-pie d-inline"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
