@extends('layouts.app')

@section('title', __('User Management › Create User'))

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
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('users.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Create User') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('Users') }}</a></div>
      <div class="breadcrumb-item">{{ __('Create') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Create User') }}</h2>
    <p class="section-lead">
      {{ __('Fill in the form below to create a new user account.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8 col-lg-8">
        <div class="card">
          <form action="{{ route('users.store') }}" method="POST" class="needs-validation" novalidate="">
            @csrf
            <div class="card-header">
              <h4>{{ __('User Form') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  class="form-control @error('name') is-invalid @enderror"
                  value="{{ old('name') }}"
                  required=""
                >
                <div class="invalid-feedback">
                  @error('name')
                    {{ $message }}
                  @else
                    {{ __('Please fill in the user name.') }}
                  @enderror
                </div>
              </div>

              <div class="form-group">
                <label for="email">{{ __('Email') }} <span class="text-danger">*</span></label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  class="form-control @error('email') is-invalid @enderror"
                  value="{{ old('email') }}"
                  required=""
                >
                <div class="invalid-feedback">
                  @error('email')
                    {{ $message }}
                  @else
                    {{ __('Please fill in a valid email address.') }}
                  @enderror
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-6">
                  <label for="password">{{ __('Password') }} <span class="text-danger">*</span></label>
                  <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    required=""
                  >
                  <div class="invalid-feedback">
                    @error('password')
                      {{ $message }}
                    @else
                      {{ __('Please fill in the password.') }}
                    @enderror
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <label for="password_confirmation">{{ __('Password Confirmation') }} <span class="text-danger">*</span></label>
                  <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control"
                    required=""
                  >
                </div>
              </div>

              <div class="form-group mb-0">
                <label class="d-block">{{ __('Assign Roles') }}</label>
                <div class="row">
                  @foreach ($roles as $role)
                    <div class="col-md-4 col-sm-6 mb-2">
                      <div class="custom-control custom-checkbox">
                        <input
                          type="checkbox"
                          class="custom-control-input"
                          id="role_{{ $role->id }}"
                          name="roles[]"
                          value="{{ $role->name }}"
                          @checked(in_array($role->name, old('roles', [])))
                        >
                        <label class="custom-control-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
                      </div>
                    </div>
                  @endforeach
                </div>
                @error('roles')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('users.index') }}" class="btn btn-secondary mr-1">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

