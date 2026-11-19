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

    $(document).on('click', '.btn-refresh-captcha', function() {
      var img = $(this).closest('.form-group').find('.captcha-img img');
      var src = img.attr('src').split('?')[0];
      img.attr('src', src + '?' + Date.now());
    });

    @if (config('stisla.is_demo'))
      $(document).on('click', '.btn-use-demo', function() {
        $('#email').val($(this).data('email')).trigger('change');
        $('#password').val($(this).data('password')).trigger('change');
      });
    @endif
  </script>
@endpush

@section('content')
  <section class="section">
    <div class="container mt-5">
      <div class="row">
        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
          <div class="login-brand">
            @php
              $siteLogo = \App\Models\Setting::getValue('site_logo');
              $siteTitle = \App\Models\Setting::getValue('site_title', 'Stisla');
              $logoUrl = $siteLogo ? \Illuminate\Support\Facades\Storage::disk('public')->url($siteLogo) : asset('assets/img/stisla-fill.svg');
            @endphp
            <img src="{{ $logoUrl }}" alt="{{ $siteTitle }}" width="100" class="shadow-light rounded-circle">
          </div>

          <div class="card card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h4>{{ __('Login') }}</h4>
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
              @if (config('stisla.is_demo'))
                <div class="alert alert-info mb-3 d-flex justify-content-between align-items-center" role="alert">
                  <div>
                    <div class="font-weight-bold mb-1">
                      <i class="fas fa-info-circle mr-1"></i> {{ __('Demo Account') }}
                    </div>
                    <div class="small">
                      <div>{{ __('Email') }}: <code>admin@example.com</code></div>
                      <div>{{ __('Password') }}: <code>password</code></div>
                    </div>
                  </div>
                  <button
                    type="button"
                    class="btn btn-sm btn-light text-primary font-weight-bold btn-use-demo"
                    data-email="admin@example.com"
                    data-password="password"
                  >
                    <i class="fas fa-magic mr-1"></i> {{ __('Use') }}
                  </button>
                </div>
              @endif

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
                    <div class="float-right">
                      <a href="{{ route('password.request') }}" class="text-small">
                        {{ __('Forgot Password?') }}
                      </a>
                    </div>
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
                  <label for="captcha">{{ __('Captcha') }} <span class="text-danger">*</span></label>
                  <div class="d-flex align-items-center mb-2">
                    <span class="captcha-img mr-2">{!! captcha_img('flat') !!}</span>
                    <button type="button" class="btn btn-outline-secondary btn-sm btn-refresh-captcha" title="{{ __('Refresh Captcha') }}">
                      <i class="fas fa-sync-alt"></i>
                    </button>
                  </div>
                  <input
                    id="captcha"
                    type="text"
                    class="form-control @error('captcha') is-invalid @enderror"
                    name="captcha"
                    placeholder="{{ __('Masukkan kode captcha') }}"
                    required
                  >
                  @error('captcha')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-group">
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me" @checked(old('remember'))>
                    <label class="custom-control-label" for="remember-me">{{ __('Remember Me') }}</label>
                  </div>
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                    {{ __('Login') }}
                  </button>
                </div>
              </form>
            </div>
          </div>
          @if (\App\Models\Setting::getValue('is_active_register_page', '1') === '1')
            <div class="mt-5 text-muted text-center">
              {{ __('Don\'t have an account?') }} <a href="{{ route('register') }}">{{ __('Create One') }}</a>
            </div>
          @endif
          <div class="simple-footer">
            Copyright &copy; {{ $siteTitle }} {{ \App\Models\Setting::getValue('year', date('Y')) }}
            <div class="mt-1">
              <a href="{{ route('privacy-policy') }}">{{ __('Privacy Policy') }}</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

