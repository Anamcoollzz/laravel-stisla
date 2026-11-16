@extends('layouts.app')

@section('title', __('Shop › Edit Footer Category'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('footer-product-categories.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Edit Footer Category') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('footer-product-categories.index') }}">{{ __('Footer Categories') }}</a></div>
      <div class="breadcrumb-item">{{ $footerCategory->display_name }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Update Footer Category') }}</h2>
    <p class="section-lead">
      {{ __('Update the selected product category and display label for the storefront footer.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8">
        <div class="card">
          <form action="{{ route('footer-product-categories.update', $footerCategory) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-header">
              <h4>{{ __('Footer Category Information') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="product_category_id">{{ __('Product Category') }} <span class="text-danger">*</span></label>
                <select name="product_category_id" id="product_category_id" class="form-control @error('product_category_id') is-invalid @enderror" required>
                  <option value="">{{ __('-- Select Category --') }}</option>
                  @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('product_category_id', $footerCategory->product_category_id) == $category->id)>
                      {{ $category->parent ? $category->parent->name . ' › ' : '' }}{{ $category->name }}
                    </option>
                  @endforeach
                </select>
                @error('product_category_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="custom_name">{{ __('Custom Display Label (Optional)') }}</label>
                    <input type="text" name="custom_name" id="custom_name" class="form-control @error('custom_name') is-invalid @enderror" value="{{ old('custom_name', $footerCategory->custom_name) }}" placeholder="e.g. Bags & Purse (leave empty to use category name)">
                    @error('custom_name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="order">{{ __('Display Order') }}</label>
                    <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $footerCategory->order) }}" min="0">
                    @error('order')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="control-label">{{ __('Status') }}</div>
                <label class="custom-switch mt-2">
                  <input type="checkbox" name="is_active" value="1" class="custom-switch-input" {{ old('is_active', $footerCategory->is_active) ? 'checked' : '' }}>
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">{{ __('Active (Visible in Footer)') }}</span>
                </label>
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('footer-product-categories.index') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Update Footer Category') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
