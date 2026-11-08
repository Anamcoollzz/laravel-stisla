@extends('layouts.auth')

@section('title')
  @yield('code')
@endsection

@section('content')
  <section class="section">
    <div class="container mt-5">
      <div class="page-error">
        <div class="page-inner">
          <h1>@yield('code')</h1>
          <div class="page-description">
            @yield('message')
          </div>
          <div class="page-search">
            <form action="{{ auth()->check() ? route('dashboard') : url('/') }}" method="GET">
              <div class="form-group floating-addon floating-addon-not-append">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      <i class="fas fa-search"></i>
                    </div>
                  </div>
                  <input type="text" class="form-control" name="q" placeholder="{{ __('Search') }}">
                  <div class="input-group-append">
                    <button type="submit" class="btn btn-primary btn-lg">
                      {{ __('Search') }}
                    </button>
                  </div>
                </div>
              </div>
            </form>
            <div class="mt-3">
              <a href="{{ auth()->check() ? route('dashboard') : url('/') }}">{{ __('Back to Home') }}</a>
            </div>
          </div>
        </div>
      </div>
      <div class="simple-footer mt-5">
        Copyright &copy; {{ config('app.name', 'Stisla') }} {{ date('Y') }}
      </div>
    </div>
  </section>
@endsection

