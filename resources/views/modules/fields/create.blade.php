@extends('layouts.app')

@section('title', __('Modules › Create Field'))

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
      <a href="{{ route('fields.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Create Field') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('fields.index') }}">{{ __('Fields') }}</a></div>
      <div class="breadcrumb-item">{{ __('Create') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Create Field') }}</h2>
    <p class="section-lead">
      {{ __('Fill in the form below to create a new field.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-6 col-lg-6">
        <div class="card">
          <form action="{{ route('fields.store') }}" method="POST" class="needs-validation" novalidate="">
            @csrf
            <div class="card-header">
              <h4>{{ __('Field Form') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="code">{{ __('Field Code') }} <span class="text-danger">*</span></label>
                <input
                  type="text"
                  id="code"
                  name="code"
                  class="form-control @error('code') is-invalid @enderror"
                  value="{{ old('code') }}"
                  required=""
                >
                <div class="invalid-feedback">
                  @error('code')
                    {{ $message }}
                  @else
                    {{ __('Please fill in the field code.') }}
                  @enderror
                </div>
              </div>

              <div class="form-group">
                <label for="name">{{ __('Field Name') }} <span class="text-danger">*</span></label>
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
                    {{ __('Please fill in the field name.') }}
                  @enderror
                </div>
              </div>

              <div class="form-group mb-0">
                <label for="description">{{ __('Description') }}</label>
                <textarea
                  id="description"
                  name="description"
                  class="form-control @error('description') is-invalid @enderror"
                  rows="3"
                >{{ old('description') }}</textarea>
                @error('description')
                  <div class="invalid-feedback">
                    {{ $message }}
                  </div>
                @enderror
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('fields.index') }}" class="btn btn-secondary mr-1">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
