@extends('layouts.app')

@section('title', __('Shop › Edit Service'))

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
    <h1>{{ __('Edit Service') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('store-services.index') }}">{{ __('Services') }}</a></div>
      <div class="breadcrumb-item">{{ $service->title }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Update Service') }}</h2>
    <p class="section-lead">
      {{ __('Modify store service card information displayed on the storefront homepage.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8">
        <div class="card">
          <form action="{{ route('store-services.update', $service) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-header">
              <h4>{{ __('Service Information') }}</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-9">
                  <div class="form-group">
                    <label for="title">{{ __('Title') }} <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $service->title) }}" required>
                    @error('title')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="order">{{ __('Display Order') }}</label>
                    <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $service->order) }}" min="0">
                    @error('order')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="description">{{ __('Description') }} <span class="text-danger">*</span></label>
                <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" required style="height: 100px;">{{ old('description', $service->description) }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="icon_svg">{{ __('Icon SVG Code') }}</label>
                <textarea name="icon_svg" id="icon_svg" rows="5" class="form-control font-monospace @error('icon_svg') is-invalid @enderror" style="height: 140px; font-size: 12px;" placeholder="<svg ...>...</svg>">{{ old('icon_svg', $service->icon_svg) }}</textarea>
                <small class="form-text text-muted">{{ __('Paste inline <svg> markup here, or upload an icon image below. Uploaded image takes priority if present.') }}</small>
                @error('icon_svg')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="mt-2 d-flex align-items-center">
                  <div id="svg-preview" class="svg-live-preview mr-3">
                    @if ($service->icon_svg)
                      {!! $service->icon_svg !!}
                    @else
                      <i class="fas fa-code text-muted"></i>
                    @endif
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
                <small class="form-text text-muted">{{ __('Max file size: 2MB. Leave empty to keep current icon.') }}</small>
                @error('icon_image')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                @if ($service->icon_image_url)
                  <div class="mt-3 d-flex align-items-center">
                    <img id="image-preview" src="{{ $service->icon_image_url }}" alt="{{ $service->title }}" class="img-thumbnail mr-3" style="width: 80px; height: 80px; object-fit: contain;">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="remove_icon_image" value="1" class="custom-control-input" id="remove_icon_image">
                      <label class="custom-control-label text-danger" for="remove_icon_image">{{ __('Remove uploaded icon image (use SVG icon instead)') }}</label>
                    </div>
                  </div>
                @else
                  <div class="mt-3">
                    <img id="image-preview" src="#" alt="Preview" class="img-thumbnail d-none" style="width: 80px; height: 80px; object-fit: contain;">
                  </div>
                @endif
              </div>

              <div class="form-group">
                <div class="control-label">{{ __('Status') }}</div>
                <label class="custom-switch mt-2">
                  <input type="checkbox" name="is_active" value="1" class="custom-switch-input" {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">{{ __('Active (Visible on Storefront)') }}</span>
                </label>
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('store-services.index') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Update Service') }}</button>
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
  </script>
@endpush

