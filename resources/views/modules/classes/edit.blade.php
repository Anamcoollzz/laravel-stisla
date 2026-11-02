@extends('layouts.app')

@section('title', __('Modules › Edit Class'))

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
      <a href="{{ route('classes.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Edit Class') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('index.html') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('classes.index') }}">{{ __('Classes') }}</a></div>
      <div class="breadcrumb-item">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Edit Class') }}</h2>
    <p class="section-lead">
      {{ __('Update the class information below.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-6 col-lg-6">
        <div class="card">
          <form action="{{ route('classes.update', $schoolClass) }}" method="POST" class="needs-validation" novalidate="">
            @csrf
            @method('PUT')
            <div class="card-header">
              <h4>{{ __('Class Form') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group mb-0">
                <label for="class_name">{{ __('Class Name') }}</label>
                <input
                  type="text"
                  id="class_name"
                  name="class_name"
                  class="form-control @error('class_name') is-invalid @enderror"
                  value="{{ old('class_name', $schoolClass->class_name) }}"
                  required=""
                >
                <div class="invalid-feedback">
                  @error('class_name')
                    {{ $message }}
                  @else
                    {{ __('Please fill in the class name.') }}
                  @enderror
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('classes.index') }}" class="btn btn-secondary mr-1">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
