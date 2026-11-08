@extends('layouts.app')

@section('title', __('Shop › Edit Brand'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('product-brands.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Edit Brand') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('product-brands.index') }}">{{ __('Brands') }}</a></div>
      <div class="breadcrumb-item">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Edit Brand') }}: {{ $brand->name }}</h2>
    <p class="section-lead">{{ __('Update brand information.') }}</p>

    <div class="row">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="card">
          <form action="{{ route('product-brands.update', $brand) }}" method="POST" class="needs-validation" novalidate="">
            @csrf
            @method('PUT')
            <div class="card-header">
              <h4>{{ __('Edit Form') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="name">{{ __('Brand Name') }} <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Nike" value="{{ old('name', $brand->name) }}" required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="slug">{{ __('Slug') }}</label>
                <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" placeholder="e.g. nike" value="{{ old('slug', $brand->slug) }}">
                @error('slug')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="card-footer bg-whitesmoke text-right">
              <a href="{{ route('product-brands.index') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Update Brand') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

