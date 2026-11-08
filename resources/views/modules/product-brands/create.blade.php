@extends('layouts.app')

@section('title', __('Shop › Create Brand'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('product-brands.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Create Brand') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('product-brands.index') }}">{{ __('Brands') }}</a></div>
      <div class="breadcrumb-item">{{ __('Create') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Create Brand') }}</h2>
    <p class="section-lead">{{ __('Add a new product brand.') }}</p>

    <div class="row">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="card">
          <form action="{{ route('product-brands.store') }}" method="POST" class="needs-validation" novalidate="">
            @csrf
            <div class="card-header">
              <h4>{{ __('Brand Form') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="name">{{ __('Brand Name') }} <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Nike" value="{{ old('name') }}" required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="slug">{{ __('Slug') }}</label>
                <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" placeholder="e.g. nike" value="{{ old('slug') }}">
                @error('slug')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">{{ __('Leave blank to generate automatically.') }}</small>
              </div>
            </div>
            <div class="card-footer bg-whitesmoke text-right">
              <a href="{{ route('product-brands.index') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Save Brand') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

