@extends('layouts.app')

@section('title', __('Shop › Add Testimonial'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('testimonials.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Add Testimonial') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('testimonials.index') }}">{{ __('Testimonials') }}</a></div>
      <div class="breadcrumb-item">{{ __('Create') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Create New Testimonial') }}</h2>
    <p class="section-lead">
      {{ __('Fill in the customer review details below.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-8">
        <div class="card">
          <form action="{{ route('testimonials.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-header">
              <h4>{{ __('Testimonial Information') }}</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="name">{{ __('Client / Customer Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="designation">{{ __('Designation / Role') }}</label>
                    <input type="text" name="designation" id="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation') }}" placeholder="e.g. Co-Founder, Happy Client">
                    @error('designation')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="rating">{{ __('Rating') }} (1-5) <span class="text-danger">*</span></label>
                    <select name="rating" id="rating" class="form-control @error('rating') is-invalid @enderror" required>
                      @for ($r = 5; $r >= 1; $r--)
                        <option value="{{ $r }}" {{ old('rating', 5) == $r ? 'selected' : '' }}>{{ $r }} ★</option>
                      @endfor
                    </select>
                    @error('rating')
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
                <label for="content">{{ __('Testimonial Content / Review') }} <span class="text-danger">*</span></label>
                <textarea name="content" id="content" rows="4" class="form-control @error('content') is-invalid @enderror" required style="height: 120px;">{{ old('content') }}</textarea>
                @error('content')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="image">{{ __('Client Avatar / Photo') }}</label>
                <div class="custom-file">
                  <input type="file" name="image" class="custom-file-input @error('image') is-invalid @enderror" id="image" accept="image/*" onchange="previewImage(event)">
                  <label class="custom-file-label" for="image">{{ __('Choose file') }}</label>
                </div>
                <small class="form-text text-muted">{{ __('Max file size: 2MB. Recommended 200x200 square.') }}</small>
                @error('image')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="mt-3">
                  <img id="image-preview" src="#" alt="Preview" class="img-thumbnail rounded-circle d-none" style="width: 100px; height: 100px; object-fit: cover;">
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
              <a href="{{ route('testimonials.index') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
              <button type="submit" class="btn btn-primary">{{ __('Save Testimonial') }}</button>
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
  </script>
@endpush

