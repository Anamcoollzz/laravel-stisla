@extends('layouts.app')

@section('title', __('Shop › Create Vendor'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('product-vendors.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Create Vendor') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('product-vendors.index') }}">{{ __('Vendors') }}</a></div>
      <div class="breadcrumb-item">{{ __('Create') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Create Vendor / Supplier') }}</h2>
    <p class="section-lead">{{ __('Add a new supplier profile.') }}</p>

    <div class="row">
      <div class="col-12 col-md-8 col-lg-7">
        <div class="card">
          <form action="{{ route('product-vendors.store') }}" method="POST" class="needs-validation" novalidate="">
            @csrf
            <div class="card-header">
              <h4>{{ __('Vendor Information') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="name">{{ __('Vendor / Company Name') }} <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. PT Mitra Busana Nusantara" value="{{ old('name') }}" required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="phone">{{ __('Phone') }}</label>
                  <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="e.g. +62 812-3456-7890" value="{{ old('phone') }}">
                  @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="form-group col-md-6">
                  <label for="email">{{ __('Email') }}</label>
                  <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="e.g. sales@vendor.com" value="{{ old('email') }}">
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="form-group">
                <label for="address">{{ __('Office / Warehouse Address') }}</label>
                <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="2" placeholder="Full address...">{{ old('address') }}</textarea>
                @error('address')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="description">{{ __('Description / Notes') }}</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Optional notes, supplied products or contracts...">{{ old('description') }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="card-footer bg-whitesmoke text-right">
              <a href="{{ route('product-vendors.index') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Save Vendor') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

