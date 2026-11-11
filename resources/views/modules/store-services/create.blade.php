@extends('layouts.app')

@section('title', __('Shop › Add Service'))

@push('css')
  <style>
    .svg-live-preview {
      width: 72px;
      height: 72px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f4f6f9;
      border: 1px dashed #cdd3d8;
      border-radius: 8px;
    }
    .svg-live-preview svg {
      width: 48px;
      height: 48px;
    }
    .svg-live-preview svg path {
      fill: #3a4ee5;
    }
  </style>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('store-services.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Add Service') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('store-services.index') }}">{{ __('Services') }}</a></div>
      <div class="breadcrumb-item">{{ __('Create') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Create New Service') }}</h2>
    <p class="section-lead">
      {{ __('Add a new store service card to highlight features on the homepage.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8">
        <div class="card">
          <form action="{{ route('store-services.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-header">
              <h4>{{ __('Service Information') }}</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-9">
                  <div class="form-group">
                    <label for="title">{{ __('Title') }} <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. Free Shipping" required>
                    @error('title')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="order">{{ __('Display Order') }}</label>
                    <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', 0) }}" min="0">
                    @error('order')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="description">{{ __('Description') }} <span class="text-danger">*</span></label>
                <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" required style="height: 100px;" placeholder="e.g. Free shipping on all US order or order above $200">{{ old('description') }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="icon_svg">{{ __('Icon SVG Code') }}</label>
                <textarea name="icon_svg" id="icon_svg" rows="5" class="form-control font-monospace @error('icon_svg') is-invalid @enderror" style="height: 140px; font-size: 12px;" placeholder="<svg ...>...</svg>">{{ old('icon_svg') }}</textarea>
                <small class="form-text text-muted">{{ __('Paste inline <svg> markup here, or upload an icon image below. Uploaded image takes priority if both are set.') }}</small>
                @error('icon_svg')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="mt-2 d-flex align-items-center">
                  <div id="svg-preview" class="svg-live-preview mr-3">
                    <i class="fas fa-code text-muted"></i>
                  </div>
                  <small class="text-muted">{{ __('Live SVG Preview') }}</small>
                </div>
              </div>

              <div class="form-group">
                <label for="icon_image">{{ __('Or Upload Icon Image') }}</label>
                <div class="custom-file">
                  <input type="file" name="icon_image" class="custom-file-input @error('icon_image') is-invalid @enderror" id="icon_image" accept="image/*" onchange="previewImage(event)">
                  <label class="custom-file-label" for="icon_image">{{ __('Choose file') }}</label>
                </div>
                <small class="form-text text-muted">{{ __('Max file size: 2MB. Recommended 100x100 PNG/SVG/WebP.') }}</small>
                @error('icon_image')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="mt-3">
                  <img id="image-preview" src="#" alt="Preview" class="img-thumbnail d-none" style="width: 80px; height: 80px; object-fit: contain;">
                </div>
              </div>

              <div class="form-group">
                <div class="control-label">{{ __('Status') }}</div>
                <label class="custom-switch mt-2">
                  <input type="checkbox" name="is_active" value="1" class="custom-switch-input" {{ old('is_active', true) ? 'checked' : '' }}>
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">{{ __('Active (Visible on Storefront)') }}</span>
                </label>
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('store-services.index') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Save Service') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('page-js')
  <script>
    function previewImage(event) {
      const input = event.target;
      const preview = document.getElementById('image-preview');
      const label = input.nextElementSibling;
      if (input.files && input.files[0]) {
        label.innerText = input.files[0].name;
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    const svgInput = document.getElementById('icon_svg');
    const svgPreview = document.getElementById('svg-preview');
    function updateSvgPreview() {
      const val = svgInput.value.trim();
      if (val && val.toLowerCase().includes('<svg')) {
        svgPreview.innerHTML = val;
      } else {
        svgPreview.innerHTML = '<i class="fas fa-code text-muted"></i>';
      }
    }
    svgInput.addEventListener('input', updateSvgPreview);
    updateSvgPreview();
  </script>
@endpush

