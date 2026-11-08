@extends('layouts.app')

@section('title', __('Access Control › Create Permission'))

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
      <a href="{{ route('permissions.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Create Permission') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('permissions.index') }}">{{ __('Permissions') }}</a></div>
      <div class="breadcrumb-item">{{ __('Create') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Create Permission') }}</h2>
    <p class="section-lead">
      {{ __('Add a new permission in the module.action format, e.g. reports.view.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-6 col-lg-6">
        <div class="card">
          <form action="{{ route('permissions.store') }}" method="POST" class="needs-validation" novalidate="">
            @csrf
            <div class="card-header">
              <h4>{{ __('Permission Form') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group mb-0">
                <label for="name">{{ __('Permission Name') }} <span class="text-danger">*</span></label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  class="form-control @error('name') is-invalid @enderror"
                  value="{{ old('name') }}"
                  placeholder="e.g. reports.view"
                  required=""
                >
                <small class="form-text text-muted">{{ __('Use lowercase letters, numbers and dashes, separated by a dot.') }}</small>
                <div class="invalid-feedback">
                  @error('name')
                    {{ $message }}
                  @else
                    {{ __('Please fill in the permission name.') }}
                  @enderror
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('permissions.index') }}" class="btn btn-secondary mr-1">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
