@extends('layouts.app')

@section('title', __('Settings'))

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    @if (session('error'))
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Settings') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Settings') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Overview') }}</h2>
    <p class="section-lead">
      {{ __('Organize and adjust all settings about this site.') }}
    </p>

    <div class="row">
      @foreach ($groups as $key => $group)
        <div class="col-lg-6">
          <div class="card card-large-icons">
            <div class="card-icon bg-primary text-white">
              <i class="{{ $group['icon'] }}"></i>
            </div>
            <div class="card-body">
              <h4>{{ __($group['label']) }}</h4>
              <p>{{ __($group['description']) }}</p>
              <a href="{{ route('settings.show', $key) }}" class="card-cta">{{ __('Change Setting') }} <i class="fas fa-chevron-right"></i></a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
@endsection
