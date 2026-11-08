@extends('layouts.auth')

@section('title', __('Forgot Password'))

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    @if (session('status'))
      swal(@json(__('Success')), @json(session('status')), 'success');
    @endif

    @if (session('error'))
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif
  </script>
@endpush

@section('content')
  <section class="section">
    <div class="container mt-5">
      <div class="row">
        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
          <div class="login-brand">
            <img src="{{ asset('assets/img/stisla-fill.svg') }}" alt="logo" width="100" class="shadow-light rounded-circle">
          </div>

          <div class="card card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h4>{{ __('Forgot Password') }}</h4>
              <div class="card-header-action dropdown">
                <a href="#" class="btn btn-sm btn-outline-primary dropdown-toggle" data-toggle="dropdown">
                  <i class="fas fa-globe mr-1"></i> {{ strtoupper(app()->getLocale()) }}
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                  <a class="dropdown-item d-flex align-items-center justify-content-between {{ app()->getLocale() === 'id' ? 'active font-weight-bold text-primary' : '' }}" href="{{ route('lang.switch', 'id') }}">
                    <span><span class="mr-2">🇮🇩</span> Bahasa Indonesia</span>
                    @if (app()->getLocale() === 'id')
                      <i class="fas fa-check text-primary"></i>
                    @endif
                  </a>
                  <a class="dropdown-item d-flex align-items-center justify-content-between {{ app()->getLocale() === 'en' ? 'active font-weight-bold text-primary' : '' }}" href="{{ route('lang.switch', 'en') }}">
                    <span><span class="mr-2">🇬🇧</span> English</span>
                    @if (app()->getLocale() === 'en')
                      <i class="fas fa-check text-primary"></i>
                    @endif
                  </a>
                </div>
              </div>
            </div>

            <div class="card-body">
              <p class="text-muted">{{ __('We will send a link to reset your password') }}</p>

              @if (session('status'))
                <div class="alert alert-success alert-has-icon">
                  <div class="alert-icon"><i class="fas fa-check-circle"></i></div>
                  <div class="alert-body">
                    <div class="alert-title">{{ __('Success') }}</div>
                    {{ session('status') }}
                  </div>
                </div>
              @endif

              <form method="POST" action="{{ route('password.email') }}" class="needs-validation" novalidate="">
                @csrf
                <div class="form-group">
                  <label for="email">{{ __('Email') }} <span class="text-danger">*</span></label>
                  <input
                    id="email"
                    type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    name="email"
                    value="{{ old('email') }}"
                    tabindex="1"
                    required
                    autofocus
                  >
                  <div class="invalid-feedback">
                    @error('email')
                      {{ $message }}
                    @else
                      {{ __('Please fill in a valid email.') }}
                    @enderror
                  </div>
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="2">
                    {{ __('Send Password Reset Link') }}
                  </button>
                </div>
              </form>

              <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-small text-muted font-weight-600">
                  <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to Login') }}
                </a>
              </div>
            </div>
          </div>
          <div class="simple-footer">
            Copyright &copy; {{ config('app.name', 'Stisla') }} {{ date('Y') }}
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

