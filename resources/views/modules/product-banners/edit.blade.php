@extends('layouts.app')

@section('title', __('Shop › Edit Banner'))

@push('page-js')
  <script>
    "use strict";
    $(document).on('change', '#image', function() {
      const [file] = this.files;
      if (file) {
        $('#current-img-preview').attr('src', URL.createObjectURL(file)).removeClass('d-none');
        $('.custom-file-label').text(file.name);
      }
    });
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('product-banners.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Edit Banner') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('product-banners.index') }}">{{ __('Banners') }}</a></div>
      <div class="breadcrumb-item">{{ __('Edit') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Edit Banner') }}</h2>
    <p class="section-lead">{{ __('Update banner details, image, or order.') }}</p>

    <div class="row">
      <div class="col-12 col-md-10 col-lg-8">
        <div class="card">
          <form action="{{ route('product-banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate="">
            @csrf
            @method('PUT')
            <div class="card-header">
              <h4>{{ __('Edit Banner Form') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="title">{{ __('Banner Title') }} <span class="text-danger">*</span></label>
                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="e.g. Fashion sale for women's" value="{{ old('title', $banner->title) }}" required>
                @error('title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">{{ __('HTML break line <br> is supported for multiline heading.') }}</small>
              </div>

              <div class="form-group">
                <label for="subtitle">{{ __('Subtitle / Description') }}</label>
                <textarea id="subtitle" name="subtitle" rows="3" class="form-control @error('subtitle') is-invalid @enderror" placeholder="e.g. Elevate your every day. Style that speaks volumes.">{{ old('subtitle', $banner->subtitle) }}</textarea>
                @error('subtitle')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="badge_label">{{ __('Badge Label') }}</label>
                    <input type="text" id="badge_label" name="badge_label" class="form-control @error('badge_label') is-invalid @enderror" placeholder="e.g. 50% Off or New Arrival" value="{{ old('badge_label', $banner->badge_label) }}">
                    @error('badge_label')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="position">{{ __('Position') }} <span class="text-danger">*</span></label>
                    <select id="position" name="position" class="form-control @error('position') is-invalid @enderror">
                      <option value="hero" @selected(old('position', $banner->position) === 'hero')>{{ __('Hero Slider (Homepage)') }}</option>
                    </select>
                    @error('position')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="product_id">{{ __('Linked Product (Shop Now Target)') }}</label>
                <select id="product_id" name="product_id" class="form-control @error('product_id') is-invalid @enderror">
                  <option value="">{{ __('-- Select Product (Optional) --') }}</option>
                  @foreach ($products as $prod)
                    <option value="{{ $prod->id }}" @selected(old('product_id', $banner->product_id) == $prod->id)>{{ $prod->title }}</option>
                  @endforeach
                </select>
                @error('product_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">{{ __('If selected, clicking the Shop Now button will navigate directly to this product detail page.') }}</small>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="button_text">{{ __('Button Text') }}</label>
                    <input type="text" id="button_text" name="button_text" class="form-control @error('button_text') is-invalid @enderror" placeholder="e.g. Shop Now" value="{{ old('button_text', $banner->button_text) }}">
                    @error('button_text')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="button_url">{{ __('Button URL') }}</label>
                    <input type="text" id="button_url" name="button_url" class="form-control @error('button_url') is-invalid @enderror" placeholder="e.g. # or {{ route('ecommerce.shop-left-sidebar.html') }}" value="{{ old('button_url', $banner->button_url) }}">
                    @error('button_url')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="image">{{ __('Banner Image') }}</label>
                <div class="mb-2">
                  <img id="current-img-preview" src="{{ $banner->image_url }}" alt="Preview" class="img-thumbnail rounded" style="max-height: 140px;">
                  @if ($banner->image)
                    <div class="custom-control custom-checkbox mt-2">
                      <input type="checkbox" name="remove_image" value="1" class="custom-control-input" id="remove_image">
                      <label class="custom-control-label text-danger" for="remove_image">{{ __('Remove uploaded image and use fallback') }}</label>
                    </div>
                  @endif
                </div>
                <div class="custom-file mb-2">
                  <input type="file" id="image" name="image" accept="image/*" class="custom-file-input @error('image') is-invalid @enderror">
                  <label class="custom-file-label" for="image">{{ __('Change File') }}</label>
                </div>
                @error('image')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">{{ __('Leave blank to keep the current image. Recommended resolution: 1920x600px. Max size: 2MB.') }}</small>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="order">{{ __('Display Order') }}</label>
                    <input type="number" id="order" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $banner->order) }}" min="1">
                    @error('order')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="d-block">{{ __('Status') }}</label>
                    <label class="custom-switch mt-2 pl-0">
                      <input type="checkbox" name="is_active" value="1" class="custom-switch-input" @checked(old('is_active', $banner->is_active ? '1' : '0') == '1')>
                      <span class="custom-switch-indicator"></span>
                      <span class="custom-switch-description">{{ __('Active and visible on site') }}</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer bg-whitesmoke text-right">
              <a href="{{ route('product-banners.index') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Update Banner') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

