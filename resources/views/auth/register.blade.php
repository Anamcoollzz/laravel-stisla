@extends('layouts.auth')

@section('title', __('Register'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/bootstrap-social/bootstrap-social.css') }}">
@endpush

@push('css')
@endpush

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
  <script src="{{ asset('node_modules/jquery-pwstrength/jquery.pwstrength.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    @if ($errors->any())
      swal(@json(__('Error')), @json($errors->first()), 'error');
    @endif

    $(".pwstrength").pwstrength();

    $(document).on('click', '.btn-refresh-captcha', function() {
      var img = $(this).closest('.form-group').find('.captcha-img img');
      var src = img.attr('src').split('?')[0];
      img.attr('src', src + '?' + Date.now());
    });
  </script>
@endpush

@section('content')
  <section class="section">
    <div class="container mt-5">
      <div class="row">
        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-5 offset-xl-3">
          <div class="login-brand">
            <img src="{{ asset('assets/img/stisla-fill.svg') }}" alt="logo" width="100" class="shadow-light rounded-circle">
          </div>

          <div class="card card-primary">
            <div class="card-header"><h4>{{ __('Register') }}</h4></div>

            <div class="card-body">
              <form method="POST" action="{{ route('register.store') }}">
                @csrf
                <div class="form-group">
                  <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                  <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="email">{{ __('Email') }} <span class="text-danger">*</span></label>
                  <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="row">
                  <div class="form-group col-6">
                    <label for="password" class="d-block">{{ __('Password') }} <span class="text-danger">*</span></label>
                    <input id="password" type="password" class="form-control pwstrength @error('password') is-invalid @enderror" data-indicator="pwindicator" name="password" required>
                    @error('password')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="pwindicator" class="pwindicator">
                      <div class="bar"></div>
                      <div class="label"></div>
                    </div>
                  </div>
                  <div class="form-group col-6">
                    <label for="password_confirmation" class="d-block">{{ __('Password Confirmation') }} <span class="text-danger">*</span></label>
                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
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
                  <input id="captcha" type="text" class="form-control @error('captcha') is-invalid @enderror" name="captcha" placeholder="{{ __('Masukkan kode captcha') }}" required>
                  @error('captcha')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-group">
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="agree" class="custom-control-input @error('agree') is-invalid @enderror" id="agree" @checked(old('agree')) required>
                    <label class="custom-control-label" for="agree">{{ __('I agree with the terms and conditions') }}</label>
                  </div>
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-primary btn-lg btn-block">
                    {{ __('Register') }}
                  </button>
                </div>

                <div class="text-center mt-3 mb-3">
                  <div class="text-job text-muted">{{ __('Or Continue With') }}</div>
                </div>

                <div class="form-group mb-0">
                  <a href="{{ route('auth.google') }}" class="btn btn-block btn-social btn-google">
                    <span class="fab fa-google"></span> {{ __('Register with Google') }}
                  </a>
                </div>
              </form>
            </div>
          </div>
          <div class="mt-5 text-muted text-center">
            {{ __('Already have an account?') }} <a href="{{ route('login') }}">{{ __('Login') }}</a>
          </div>
          <div class="simple-footer">
            Copyright &copy; Stisla 2018
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
