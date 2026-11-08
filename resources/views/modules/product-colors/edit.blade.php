@extends('layouts.app')

@section('title', __('Shop › Edit Color'))

@push('page-js')
  <script>
    "use strict";
    $('#color_picker').on('input', function() {
      $('#color_code').val($(this).val());
    });
    $('#color_code').on('input', function() {
      var val = $(this).val();
      if (/^#[0-9A-F]{6}$/i.test(val)) {
        $('#color_picker').val(val);
      }
    });
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('product-colors.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Edit Color') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('product-colors.index') }}">{{ __('Colors') }}</a></div>
      <div class="breadcrumb-item">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Edit Color') }}: {{ $color->name }}</h2>
    <p class="section-lead">{{ __('Update color variant information.') }}</p>

    <div class="row">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="card">
          <form action="{{ route('product-colors.update', $color) }}" method="POST" class="needs-validation" novalidate="">
            @csrf
            @method('PUT')
            <div class="card-header">
              <h4>{{ __('Edit Form') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="name">{{ __('Color Name') }} <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Navy Blue" value="{{ old('name', $color->name) }}" required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="color_code">{{ __('Color Code (Hex)') }}</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <input type="color" id="color_picker" class="form-control" style="width: 50px; padding: 2px; height: 42px;" value="{{ old('color_code', $color->color_code ?? '#000000') }}">
                  </div>
                  <input type="text" id="color_code" name="color_code" class="form-control @error('color_code') is-invalid @enderror" placeholder="#000000" value="{{ old('color_code', $color->color_code) }}">
                  @error('color_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>
            <div class="card-footer bg-whitesmoke text-right">
              <a href="{{ route('product-colors.index') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Update Color') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

