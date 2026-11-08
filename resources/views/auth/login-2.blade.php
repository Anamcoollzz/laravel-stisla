@extends('layouts.auth')

@section('title', __('Login'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/bootstrap-social/bootstrap-social.css') }}">
@endpush

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    @if (session('success'))
      swal(@json(__('Success')), @json(session('success')), 'success');
    @endif

    @if (session('error'))
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif

    @if ($errors->any())
      swal(@json(__('Error')), @json($errors->first()), 'error');
    @endif
  </script>
@endpush

@section('content')
  @php
    $siteTitle = \App\Models\Setting::getValue('site_title', 'Stisla');
    $siteLogo = \App\Models\Setting::getValue('site_logo');
    $logoUrl = $siteLogo ? \Illuminate\Support\Facades\Storage::disk('public')->url($siteLogo) : asset('assets/img/stisla-fill.svg');
    $year = \App\Models\Setting::getValue('year', date('Y'));
    $city = \App\Models\Setting::getValue('city', 'Bali');
    $country = \App\Models\Setting::getValue('country', 'Indonesia');
    $hour = (int) now()->format('H');
    $greeting = match(true) {
        $hour < 12 => __('Good Morning'),
        $hour < 17 => __('Good Afternoon'),
        default => __('Good Evening'),
    };
  @endphp

  <section class="section">
    <div class="d-flex flex-wrap align-items-stretch">
      <div class="col-lg-4 col-md-6 col-12 order-lg-1 min-vh-100 order-2 bg-white d-flex flex-column justify-content-between">
        <div class="p-4 m-3">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <img src="{{ $logoUrl }}" alt="{{ $siteTitle }}" width="70" class="shadow-light rounded-circle">
            <div class="dropdown">
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

          <h4 class="text-dark font-weight-normal">{{ __('Welcome to') }} <span class="font-weight-bold">{{ $siteTitle }}</span></h4>
          <p class="text-muted">{{ __('Before you get started, you must login or register if you don\'t already have an account.') }}</p>

          <form method="POST" action="{{ route('login.attempt') }}" class="needs-validation" novalidate="">
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
              <div class="d-block">
                <label for="password" class="control-label">{{ __('Password') }} <span class="text-danger">*</span></label>
              </div>
              <input
                id="password"
                type="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password"
                tabindex="2"
                required
              >
              <div class="invalid-feedback">
                @error('password')
                  {{ $message }}
                @else
                  {{ __('Please fill in your password.') }}
                @enderror
              </div>
            </div>

            <div class="form-group">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me" @checked(old('remember'))>
                <label class="custom-control-label" for="remember-me">{{ __('Remember Me') }}</label>
              </div>
            </div>

            <div class="form-group text-right">
              <a href="{{ route('password.request') }}" class="float-left mt-3">
                {{ __('Forgot Password?') }}
              </a>
              <button type="submit" class="btn btn-primary btn-lg btn-icon icon-right" tabindex="4">
                {{ __('Login') }}
              </button>
            </div>

            <div class="mt-4 text-center">
              {{ __('Don\'t have an account?') }} <a href="{{ route('register') }}">{{ __('Create One') }}</a>
            </div>
          </form>
        </div>

        <div class="text-center p-4 text-small text-muted">
          Copyright &copy; {{ $siteTitle }} {{ $year }}. {{ __('Made with 💙 by Stisla') }}
          <div class="mt-2">
            <a href="{{ route('privacy-policy') }}">{{ __('Privacy Policy') }}</a>
          </div>
        </div>
      </div>

      <div class="col-lg-8 col-12 order-lg-2 order-1 min-vh-100 background-walk-y position-relative overlay-gradient-bottom" data-background="{{ asset('assets/img/unsplash/login-bg.jpg') }}">
        <div class="absolute-bottom-left index-2">
          <div class="text-light p-5 pb-2">
            <div class="mb-5 pb-3">
              <h1 class="mb-2 display-4 font-weight-bold">{{ $greeting }}</h1>
              <h5 class="font-weight-normal text-muted-transparent">{{ $city }}, {{ $country }}</h5>
            </div>
            Photo by <a class="text-light bb" target="_blank" href="https://unsplash.com/photos/a8lTjWJJgLA">Justin Kauffman</a> on <a class="text-light bb" target="_blank" href="https://unsplash.com">Unsplash</a>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

